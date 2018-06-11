<?php
class ModelExtensionModuleExcelimport extends Model {
	private $now;
	
	public function entity_decode($text) {
		return html_entity_decode($text, ENT_QUOTES, 'UTF-8');
	}
	
	public function editProduct($product_id, $data, &$languages, $light = false) {
		$extra_select = '';
		if (version_compare(VERSION, '1.5.4', '>=')) {
			$extra_select = ", ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "'";
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "'" . $extra_select . ", location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . $this->db->escape($data['tax_class_id']) . "', sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
		
		if (isset($data['image'])) {
			$imgarr = explode(",",$data['image']);
			
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape(html_entity_decode($imgarr[0], ENT_QUOTES, 'UTF-8')) . "' WHERE product_id = '" . (int)$product_id . "'");
			array_shift($imgarr);
			foreach ($imgarr as $key => $value){
				$v1="DELETE FROM " . DB_PREFIX . "product_image WHERE image = '".$value."'";
				$this->db->query($v1);
				$v2="INSERT INTO " . DB_PREFIX . "product_image (product_id,image,sort_order) VALUES (".$product_id.",'".$value."',0)";
				$this->db->query($v2);
			}
		}
		
		$language_ids = array();
		
		
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "' ");
		
		foreach ($data['product_description'] as $language_id => $value) {
			
			$extra_insert = "";
			if (version_compare(VERSION, '1.5.4', '>=')) {
				$extra_insert = ", tag = '" . $this->db->escape($value['tag']) . "'";
			}
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', description = '" . $this->db->escape($value['description']) . "'" . $extra_insert . " ON DUPLICATE KEY UPDATE name = '" . $this->db->escape($value['name']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', description = '" . $this->db->escape($value['description']) . "'" . $extra_insert);
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
		
		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		
		if (!$light) {
			
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND language_id NOT IN (" . implode(',', $language_ids) . ")");
			
			if (!empty($data['product_attribute'])) {
				foreach ($data['product_attribute'] as $product_attribute) {
					if ($product_attribute['attribute_id']) {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "' AND language_id NOT IN (" . implode(',', $language_ids) . ")");
						
						foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "' ON DUPLICATE KEY UPDATE text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
						}
					}
				}
			}
			
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
			
			if (isset($data['product_option'])) {
				foreach ($data['product_option'] as $product_option) {
					if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");
						
						$product_option_id = $this->db->getLastId();
						
						if (isset($product_option['product_option_value'])) {
							foreach ($product_option['product_option_value'] as $product_option_value) {
								$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
							}
						}
					} else {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
					}
				}
			}
			
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
			
			if (isset($data['product_discount'])) {
				foreach ($data['product_discount'] as $product_discount) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
				}
			}
			
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
			
			if (isset($data['product_special'])) {
				foreach ($data['product_special'] as $product_special) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
				}
			}
			
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
			
			if (isset($data['product_image'])) {
				foreach ($data['product_image'] as $product_image) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape(html_entity_decode($product_image['image'], ENT_QUOTES, 'UTF-8')) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
				}
			}
			
			if (version_compare(VERSION, '2.0', '>=')) {
				$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = " . (int) $product_id);
				
				if (isset($data['product_recurrings'])) {
					foreach ($data['product_recurrings'] as $recurring) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int) $product_id . ", customer_group_id = " . (int) $recurring['customer_group_id'] . ", `recurring_id` = " . (int) $recurring['recurring_id']);
					}
				}
			}
			
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
		
		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("REPLACE INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		
		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}
		
		if (version_compare(VERSION, '1.5.5', '>=')) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");
			
			if (isset($data['product_filter'])) {
				foreach ($data['product_filter'] as $filter_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
				}
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");
		
		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				//$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT IGNORE INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				//$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				//$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}
		
		if (!$light) {
			
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
			
			if (isset($data['product_reward'])) {
				foreach ($data['product_reward'] as $customer_group_id => $value) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
				}
			}
			
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
			
			if (isset($data['product_layout'])) {
				foreach ($data['product_layout'] as $store_id => $layout) {
					if ($layout['layout_id']) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout['layout_id'] . "'");
					}
				}
			}
			
		}
		
		if (version_compare(VERSION, '1.5.4', '<')) {
			$keys = array_keys($data['product_tag']);
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_tag WHERE product_id = '" . (int)$product_id. "' AND (language_id NOT IN (" . implode(',', $language_ids) . ") || language_id = '" . $keys[0] . "')");
			
			foreach ($data['product_tag'] as $language_id => $value) {
				if ($value) {
					$tags = explode(',', $value);
					
					foreach ($tags as $tag) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_tag SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', tag = '" . $this->db->escape(trim($tag)) . "'");
					}
				}
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id. "'");
		
		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
		
		// Extras
		foreach ($this->extraGeneralFields['Products'] as $extra) {
			if (!empty($extra['eval_edit'])) {
				eval($extra['eval_edit']);
			}
		}
		
		if (file_exists(DIR_APPLICATION . 'model/tool/nitro.php')) {
			$this->load->model('tool/nitro');
			
			if (method_exists($this->model_tool_nitro, 'clearProductCache')) {
				$this->model_tool_nitro->clearProductCache($product_id);
			}
		}
		
		$this->cache->delete('product');
	}
	
	
	public function getData($type, &$row) {
		$id_key = 'id';
		
		$id_mapping = array(
				'Products' => 'product_id',
				'Categories' => 'category_id',
				'Options' => 'option_id',
				'Attributes' => 'attribute_id',
				'AttributeGroups' => 'attribute_group_id',
				'Customers' => 'customer_id',
				'CustomerGroups' => 'customer_group_id',
				'Orders' => 'order_id',
				'Manufacturers' => 'manufacturer_id'
		);
		
		$id_key = in_array($type, array_keys($id_mapping)) ? $id_mapping[$type] : 'id';
		
		foreach ($this->dataArray[$type] as $name => $data) {
			$row[$name] = '';
			foreach ($data as $array) {
				if ($array[$id_key] == $row[$id_key]) {
					$row[$name] = $array['value'];
					break;
				}
			}
		}
	}
	public function getQuery($filters = array(), $store = 0, $language = 1, $count = false,$addimages = false) {
		if (empty($filters) || !in_array($filters['Conjunction'], array('AND', 'OR'))) $filters['Conjunction'] = 'OR';
		
		$join_rules = array(
				'product_description' => "LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . $language . "')",
				'product_to_store' => "LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id AND p2s.store_id = '" . $store . "')",
				'stock_status' => "LEFT JOIN " . DB_PREFIX . "stock_status ss ON (ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . $language . "')",
				'url_alias' => "LEFT JOIN " . DB_PREFIX . "url_alias ua ON (ua.query = CONCAT('product_id=', p.product_id))",
				'manufacturer' => "LEFT JOIN " . DB_PREFIX . "manufacturer m ON (m.manufacturer_id = p.manufacturer_id)",
				'product_tag' => "LEFT OUTER JOIN " . DB_PREFIX . "product_tag pt ON (p.product_id = pt.product_id AND pt.language_id = '" . $language . "')",
				'category' => "JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) LEFT JOIN " . DB_PREFIX . "category_description cd ON (cd.category_id = p2c.category_id AND cd.language_id = '" . $language . "')",
				'filter' => "JOIN " . DB_PREFIX . "product_filter pf ON (p.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "filter_description fd ON (fd.filter_id = pf.filter_id AND fd.language_id = '" . $language . "')",
				'download' => "JOIN " . DB_PREFIX . "product_to_download p2d ON (p.product_id = p2d.product_id) LEFT JOIN " . DB_PREFIX . "download_description dd ON (dd.download_id = p2d.download_id AND dd.language_id = '" . $language . "')",
				'product_related' => "LEFT JOIN " . DB_PREFIX . "product_related pr ON (p.product_id = pr.product_id) LEFT JOIN " . DB_PREFIX . "product_description prd ON (pr.related_id = prd.product_id AND prd.language_id = '" . $language . "')",
				'product_attribute' => "JOIN " . DB_PREFIX . "product_attribute pa ON (p.product_id = pa.product_id AND pa.language_id = '" . $language . "') LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (ad.attribute_id = pa.attribute_id AND ad.language_id = '" . $language . "')",
				'product_option' => "JOIN " . DB_PREFIX . "product_option po ON (p.product_id = po.product_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (po.option_id = od.option_id AND od.language_id = '" . $language . "') LEFT JOIN " . DB_PREFIX . "product_option_value pov ON (pov.product_option_id = po.product_option_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (pov.option_value_id = ovd.option_value_id AND ovd.language_id = '" . $language . "')",
				'product_discount' => "JOIN " . DB_PREFIX . "product_discount pdis ON (p.product_id = pdis.product_id)",
				'product_special' => "JOIN " . DB_PREFIX . "product_special pspe ON (p.product_id = pspe.product_id)",
				'product_image' => "JOIN " . DB_PREFIX . "product_image pi ON (p.product_id = pi.product_id)",
				'product_reward' => "JOIN " . DB_PREFIX . "product_reward pr ON (p.product_id = pr.product_id)",
				'product_images'=>"RIGHT JOIN ". DB_PREFIX ."product_image pi ON (p.product_id = pi.product_id)",
				'product_to_layout' => "JOIN " . DB_PREFIX . "product_to_layout p2l ON (p.product_id = p2l.product_id AND p2l.store_id = '" . $store . "') LEFT JOIN " . DB_PREFIX . "layout l ON (p2l.layout_id = l.layout_id)"
		);
		
		$joins = array();
		$joins['product_description'] = $join_rules['product_description'];
		$joins['product_to_store'] = $join_rules['product_to_store'];
		if($addimages){
			
			$joins['product_to_images']= $join_rules['product_images'];
			
		}
		if (version_compare(VERSION, '1.5.4', '<')) {
			$joins['product_tag'] = $join_rules['product_tag'];
		}
		
		$wheres = array();
		$alter_joins_array = array();
		
		foreach ($filters as $i => $filter) {
			if (is_array($filter)) {
				if (!array_key_exists($this->conditions['Products'][$filter['Field']]['join_table'], $joins) && array_key_exists($this->conditions['Products'][$filter['Field']]['join_table'], $join_rules)) {
					$joins[$this->conditions['Products'][$filter['Field']]['join_table']] = $join_rules[$this->conditions['Products'][$filter['Field']]['join_table']];
				}
				if (!is_array($this->conditions['Products'][$filter['Field']]['field_name'])) {
					if ($this->conditions['Products'][$filter['Field']]['type'] !== 'not_exists') {
						$condition = str_replace(array('{FIELD_NAME}', '{WORD}'), array($this->conditions['Products'][$filter['Field']]['field_name'], stripos($this->conditions['Products'][$filter['Field']]['type'], 'number') !== FALSE ? (int)$this->db->escape($filter['Value']) : $this->db->escape($filter['Value'])), $this->operations[$filter['Condition']]['operation']);
						if (in_array($this->conditions['Products'][$filter['Field']]['field_name'], array('pdis.date_start', 'pdis.date_end', 'pspe.date_start', 'pspe.date_end'))) {
							$condition = "(" . $condition . " OR " . $this->conditions['Products'][$filter['Field']]['field_name'] . " = '0000-00-00')";
						}
					} else {
						$condition = $this->conditions['Products'][$filter['Field']]['field_name'] . " IS NULL";
						$alter_joins_array_entry = $this->conditions['Products'][$filter['Field']]['join_table'];
						if (!in_array($alter_joins_array_entry, $alter_joins_array)) $alter_joins_array[] = $alter_joins_array_entry;
					}
				} else {
					$sub_conditions = array();
					foreach ($this->conditions['Products'][$filter['Field']]['field_name'] as $field_name) {
						$sub_conditions[] = str_replace(array('{FIELD_NAME}', '{WORD}'), array($field_name, stripos($this->conditions['Products'][$filter['Field']]['type'], 'number') !== FALSE ? (int)$this->db->escape($filter['Value']) : $this->db->escape($filter['Value'])), $this->operations[$filter['Condition']]['operation']);
					}
					$condition = '(' . implode(' OR ', $sub_conditions) . ')';
				}
				if (!in_array($condition, $wheres)) $wheres[] = $condition;
			}
		}
		
		foreach ($alter_joins_array as $alter_joins_array_item) {
			if (array_key_exists($alter_joins_array_item, $joins)) {
				$joins[$alter_joins_array_item]	= preg_replace("/^JOIN/", "LEFT JOIN", $joins[$alter_joins_array_item]);
			}
		}
		
		$select = $count ? "COUNT(*)" : "*,".($addimages ? " concat(group_concat(pi.image separator ','),',',p.image) as images,":"")." pd.name as name, pd.description as description, pd.meta_description as meta_description, pd.meta_keyword as meta_keyword, pd.meta_title as meta_title, " . (version_compare(VERSION, '1.5.4', '<') ? 'pt.tag as tag' : 'pd.tag as tag') . ", p.*";
		
		$query = ($count ? "SELECT COUNT(*) as count FROM (" : "") . "SELECT " . $select . " FROM " . DB_PREFIX . "product p " . implode(" ", $joins) . " WHERE p2s.store_id = '" . $store . "' " . (!empty($wheres) ? " AND (" . implode(" " . $filters['Conjunction'] . " ", $wheres) . ")" : "") . " GROUP BY p.product_id" . ($count ? ") as count_table" : "");
		
		return $query;
	}
	
	public $exportData = array(
			'Products' => array(
					'tag' => "SELECT pt.product_id, GROUP_CONCAT(DISTINCT pt.tag ORDER BY pt.tag ASC SEPARATOR ', ') as value FROM {DB_PREFIX}product_tag pt WHERE pt.language_id = '{LANGUAGE_ID}' GROUP BY pt.product_id",
					'keyword' => "SELECT SUBSTRING(query, 12) as product_id, keyword as value FROM {DB_PREFIX}url_alias ua WHERE ua.query LIKE 'product_id=%'",
					'categories' => "SELECT p2c.product_id, GROUP_CONCAT(DISTINCT p2c.category_id ORDER BY p2c.category_id ASC SEPARATOR ', ') as value FROM {DB_PREFIX}product_to_category p2c GROUP BY p2c.product_id",
					'stores' => "SELECT p2s.product_id, GROUP_CONCAT(DISTINCT p2s.store_id ORDER BY p2s.store_id ASC SEPARATOR ', ') as value FROM {DB_PREFIX}product_to_store p2s GROUP BY p2s.product_id",
					'filters' => "SELECT pf.product_id, GROUP_CONCAT(DISTINCT pf.filter_id ORDER BY pf.filter_id ASC SEPARATOR ', ') as value FROM {DB_PREFIX}product_filter pf GROUP BY pf.product_id",
					'downloads' => "SELECT p2d.product_id, GROUP_CONCAT(DISTINCT p2d.download_id ORDER BY p2d.download_id ASC SEPARATOR ', ') as value FROM {DB_PREFIX}product_to_download p2d GROUP BY p2d.product_id",
					'related' => "SELECT pr.product_id, GROUP_CONCAT(DISTINCT pr.related_id ORDER BY pr.related_id ASC SEPARATOR ', ') as value FROM {DB_PREFIX}product_related pr GROUP BY pr.product_id",
					'recurrings' => "SELECT pp.product_id, GROUP_CONCAT(DISTINCT CONCAT_WS(':', pp.recurring_id, pp.customer_group_id) ORDER BY pp.recurring_id ASC SEPARATOR ', ') as value  FROM {DB_PREFIX}product_recurring pp GROUP BY pp.product_id"
			),
			'Categories' => array(
					'keyword' => "SELECT SUBSTRING(query, 13) as category_id, keyword as value FROM {DB_PREFIX}url_alias ua WHERE ua.query LIKE 'category_id=%'",
					'stores' => "SELECT c2s.category_id, GROUP_CONCAT(DISTINCT c2s.store_id ORDER BY c2s.store_id ASC SEPARATOR ', ') as value FROM {DB_PREFIX}category_to_store c2s GROUP BY c2s.category_id",
					'category_layout' => "SELECT c2l.category_id, GROUP_CONCAT(DISTINCT (SELECT CONCAT_WS(':', c2l.store_id, c2l.layout_id)) SEPARATOR ', ') as value FROM {DB_PREFIX}category_to_layout c2l GROUP BY c2l.category_id",
					'filters' => "SELECT cf.category_id, GROUP_CONCAT(DISTINCT cf.filter_id ORDER BY cf.filter_id ASC SEPARATOR ', ') as value FROM {DB_PREFIX}category_filter cf GROUP BY cf.category_id"
			),
			'Options' => array(
					
			),
			'Attributes' => array(
					
			),
			'AttributeGroups' => array(
					
			),
			'Customers' => array(
					
			),
			'CustomerGroups' => array(
					
			),
			'Orders' => array(
					
			),
			'Manufacturers' => array(
					'stores' => "SELECT m2s.manufacturer_id, GROUP_CONCAT(DISTINCT m2s.store_id ORDER BY m2s.store_id ASC SEPARATOR ', ') as value FROM {DB_PREFIX}manufacturer_to_store m2s GROUP BY m2s.manufacturer_id",
					'keyword' => "SELECT SUBSTRING(query, 17) as manufacturer_id, keyword as value FROM {DB_PREFIX}url_alias ua WHERE ua.query LIKE 'manufacturer_id=%'",
			)
	);
	public $extraGeneralFields = array(
			'Products' => array(
					/* Example: ////////////////////////////////////////////
					 array(
					 'title' => 'Custom Category',
					 'column_full' => 'R',
					 'column_light' => 'AN',
					 'name' => 'custom_category',
					 'select_sql' => "SELECT p2c.product_id, GROUP_CONCAT(DISTINCT p2c.category_id ORDER BY p2c.category_id ASC SEPARATOR ', ') as value FROM {DB_PREFIX}product_to_category p2c GROUP BY p2c.product_id",
					 'select_eval' => NULL,
					 'eval_add' => 'print_r($data["custom_category"]); exit;',
					 'eval_edit' => 'print_r($data["custom_category"]); exit;'
					 )
					 //////////////////////////////////////////////////////*/
					
					/* {EXTRA_PRODUCT_FIELDS} */ // Important hook! Do not delete.
			),
			'Categories' => array(
					/* {EXTRA_CATEGORY_FIELDS} */ // Important hook! Do not delete.
			),
			'Options' => array(
					/* {EXTRA_OPTION_FIELDS} */ // Important hook! Do not delete.
			),
			'Attributes' => array(
					/* {EXTRA_ATTRIBUTE_FIELDS} */ // Important hook! Do not delete.
			),
			'AttributeGroups' => array(
					/* {EXTRA_ATTRIBUTE_GROUP_FIELDS} */ // Important hook! Do not delete.
			),
			'Customers' => array(
					/* {EXTRA_CUSTOMER_FIELDS} */ // Important hook! Do not delete.
			),
			'CustomerGroups' => array(
					/* {EXTRA_CUSTOMER_GROUP_FIELDS} */ // Important hook! Do not delete.
			),
			'Orders' => array(
					/* {EXTRA_ORDER_FIELDS} */ // Important hook! Do not delete.
			),
			'Manufacturers' => array(
					/* {EXTRA_MANUFACTURER_FIELDS} */ // Important hook! Do not delete.
			)
	);
	public $dataArray = array(
			'Products' => array(),
			'Categories' => array(),
			'Options' => array(),
			'Attributes' => array(),
			'AttributeGroups' => array(),
			'Customers' => array(),
			'CustomerGroups' => array(),
			'Orders' => array(),
			'Manufacturers' => array()
	);
	public function special_chars($text) {
		return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
	}
	public function setData($type, $destinationDirectory, $language_id) {
		foreach ($this->exportData[$type] as $name => $query) {
			$file = $destinationDirectory . '/' . $name . '.xlptemp';
			
			switch ($type) {
				case 'Products' : {
					if (version_compare(VERSION, '1.5.3.1', '>') && $name == 'tag') continue 2;
					if (version_compare(VERSION, '1.5.5', '<') && $name == 'filters') continue 2;
					if (version_compare(VERSION, '1.5.6', '<') && $name == 'recurrings') continue 2;
				} break;
				case 'Categories' : {
					if (version_compare(VERSION, '1.5.5', '<') && $name == 'filters') continue 2;
				} break;
			}
			
			
			$this->setDataArray($type, $file, $language_id, $name, $query);
		}
		foreach ($this->extraGeneralFields[$type] as $fieldData) {
			$file = $destinationDirectory . '/custom_' . $fieldData['name'] . '.xlptemp';
			if (!empty($fieldData['name']) && !empty($fieldData['select_sql']) && empty($fieldData['select_eval'])) {
				$this->setDataArray($type, $file, $language_id, $fieldData['name'], $fieldData['select_sql']);
			} else if (!empty($fieldData['name']) && !empty($fieldData['select_eval'])) {
				$this->setDataArray($type, $file, $language_id, $fieldData['name'], NULL, $fieldData['select_eval']);
			}
		}

	}
	
	private function setDataArray($type, $file, $language_id, $name, $query, $eval = NULL) {
		if (!file_exists($file)) {
			$this->db->query("SET SESSION group_concat_max_len = 1000000;");
			if (!empty($query)) {
				$query = str_replace("{LANGUAGE_ID}", $language_id, $query);
				$query = str_replace("{DB_PREFIX}", DB_PREFIX, $query);
				$result = $this->db->query($query);
				$result = $result !== false ? $result->rows : array();
			} else if (!empty($eval)) {
				$result = false;
				eval($eval);
				$result = $result !== false ? $result : array();
			}
			
			$this->dataArray[$type][$name] = $result;
			$data = json_encode($result);
			file_put_contents($file, $data);
		} else {
			if (empty($this->dataArray[$type][$name])) {
				$data = file_get_contents($file);
				$this->dataArray[$type][$name] = json_decode($data, true);
			}
		}
	}
	
	public function getProgress($error = NULL) {
		$result = array(
				'error' => false,
				'message' => '',
				'percent' => 0,
				'done' => false,
				'current' => 0,
				'all' => -1,
				'finishedImport' => false,
				'importingFile' => ''
		);
		clearstatcache();
		return $result;
	}
	
	
	
	public function folderCheck($folder) {
		if (!is_dir($folder)) {
			if (!mkdir($folder, 0755)) throw new Exception('Error: Temp directory does not exist and cannot be created! Please check the root folder permissions.');
		}
	}
	
	public function __construct($register) {
		if (!defined('IMODULE_ROOT')) define('IMODULE_ROOT', substr(DIR_APPLICATION, 0, strrpos(DIR_APPLICATION, '/', -2)) . '/');
		if (!defined('IMODULE_ADMIN_ROOT')) define('IMODULE_ADMIN_ROOT', DIR_APPLICATION);
		if (!defined('IMODULE_SERVER_NAME')) define('IMODULE_SERVER_NAME', substr((defined('HTTP_CATALOG') ? HTTP_CATALOG : HTTP_SERVER), 7, strlen((defined('HTTP_CATALOG') ? HTTP_CATALOG : HTTP_SERVER)) - 8));
		
		if (version_compare(VERSION, '2.1.0.1', '>=')) {
			if (!defined('IMODULE_TEMP_FOLDER')) define('IMODULE_TEMP_FOLDER', 'system/storage/cache/temp_excelport');
		} else {
			if (!defined('IMODULE_TEMP_FOLDER')) define('IMODULE_TEMP_FOLDER', 'system/cache/temp_excelport');
		}
		
		if (!defined('IMODULE_UPMOST_VERSION')) define('IMODULE_UPMOST_VERSION', '2.99');
		
		if (!is_dir(IMODULE_ROOT . IMODULE_TEMP_FOLDER)) {
			mkdir(IMODULE_ROOT . IMODULE_TEMP_FOLDER, 0755);
			file_put_contents(IMODULE_ROOT . IMODULE_TEMP_FOLDER . DIRECTORY_SEPARATOR . 'index.html', 'Hello!');
		}
		
		$htaccess_file = IMODULE_ROOT . IMODULE_TEMP_FOLDER . DIRECTORY_SEPARATOR . '.htaccess';
		
		if (!file_exists($htaccess_file)) {
			$htaccess = '
                AddType text/excelport excelport
                <FilesMatch "\.(html|xlptemp|zip|xlsx|' . pathinfo($this->get_progress_name(), PATHINFO_EXTENSION) . ')$">
                    allow from all
                </FilesMatch>
            ';
			file_put_contents($htaccess_file, $htaccess);
		}
		
		$this->now = time();
		
		//$this->conditions['Products']['general_product_tags'] = array(
				//'label' => 'Product Tags',
				//'join_table' => version_compare(VERSION, '1.5.4', '<') ? 'product_tag' : 'product_description',
				//'field_name' => version_compare(VERSION, '1.5.4', '<') ? 'pt.tag' : 'pd.tag',
				//'type' => 'text'
		//);
		
		//$this->conditions['Customers']['customer_customer_group'] = array(
		//		'label' => 'Customer Group Name',
		//		'join_table' => version_compare(VERSION, '1.5.3', '<') ? 'customer_group' : 'customer_group_description',
		//		'field_name' => version_compare(VERSION, '1.5.3', '<') ? 'cg.name' : 'cgd.name',
		//		'type' => 'text'
		//);
		
		//$this->conditions['CustomerGroups']['customer_group_name'] = array(
		//		'label' => 'Customer Group Name',
		//		'join_table' => version_compare(VERSION, '1.5.3', '<') ? NULL : 'customer_group_description',
		//		'field_name' => version_compare(VERSION, '1.5.3', '<') ? 'cg.name' : 'cgd.name',
		//		'type' => 'text'
		//);
		
		parent::__construct($register);
	}
	public function exportXLS($language=1, $store=0, $destinationFolder = 'tempmy', $settings=array('ExportLimit'=>800), $quickExport = true, $filter = false, $filters = array()) {
		//TODO adaptiveimport_product-IMPORTXLSLIGHT
		$this->language->load('module/excelimport');
		$this->folderCheck($destinationFolder);
		
		$progress = $this->getProgress();
		$progress['done'] = false;
		
		$file = IMODULE_ROOT . 'vendors/excelport/template_product_light.xlsx';
		
		$default_language = $this->config->get('config_language_id');
		$this->config->set('config_language_id', $language);
		require_once(IMODULE_ROOT.'vendors/phpexcel/PHPExcel.php');
		
		if (!empty($progress['populateAll'])) {
			$all = $this->db->query($this->getQuery($export_filters, $store, $language, true));
			$progress['all'] = $all->num_rows ? (int)$all->row['count'] : 0;
			unset($progress['populateAll']);
			$this->setProgress($progress);
		}
		
		$this->setData('Products', $destinationFolder, $language);
		
		$productsSheet = 0;
		$metaSheet = 1;
		
		$taxClassesStart = array(0,2);
		$this->load->model('localisation/tax_class');
		$taxClasses = array_merge(array(0 => array('tax_class_id' => 0, 'title' => '--- None ---', 'description' => '--- None ---', 'date_added' => '0000-00-00 00:00:00', 'date_modified' => '0000-00-00 00:00:00')), $this->model_localisation_tax_class->getTaxClasses());
		
		$stockStatesStart = array(2,2);
		$this->load->model('localisation/stock_status');
		$stockStates = $this->model_localisation_stock_status->getStockStatuses();
		
		$lengthClassesStart = array(3,2);
		$this->load->model('localisation/length_class');
		$lengthClasses = $this->model_localisation_length_class->getLengthClasses();
		
		$weightClassesStart = array(4,2);
		$this->load->model('localisation/weight_class');
		$weightClasses = $this->model_localisation_weight_class->getWeightClasses();
		
		$manufacturersStart = array(6,2);
		$this->load->model('catalog/manufacturer');
		$manufacturers = array_merge(array(0 => array('manufacturer_id' => 0, 'name' => '--- None ---', 'image' => NULL, 'sort_order' => 0)), $this->model_catalog_manufacturer->getManufacturers());
		
		$categoriesStart = array(7,3);
		$this->load->model('catalog/category');
		
		if (version_compare(VERSION, '1.5.5', '>=')) {
			$categories = $this->model_catalog_category->getCategories(array());
		}
		
		if (version_compare(VERSION, '1.5.5', '<')) {
			$categories = $this->model_catalog_category->getCategories();
		}
		
		if (version_compare(VERSION, '1.5.5', '>=')) {
			$filtersStart = array(13,3);
			$this->load->model('catalog/filter');
			$filters = $this->model_catalog_filter->getFilters(array());
		}
		
		$storesStart = array(9,3);
		$this->load->model('setting/store');
		$stores = array_merge(array(0 => array('store_id' => 0, 'name' => 'Default', 'url' => NULL, 'ssl' => NULL)),$this->model_setting_store->getStores());
		
		$downloadsStart = array(11,3);
		$this->load->model('catalog/download');
		$downloads = $this->model_catalog_download->getDownloads();
		
		$generals = array(
				'product_id' 		=> 0,
				'name'				=> 1,
				'meta_description'	=> 5,
				'meta_keyword'		=> 6,
				'description'		=> 4,
				'tag'				=> 7,
				'model' 			=> 2,
				'sku'				=> 3,
				'upc'				=> 8,
				'ean'				=> 9,
				'jan'				=> 10,
				'isbn'				=> 11,
				'mpn'				=> 12,
				'location'			=> 14,
				'price'				=> 13,
				'tax_class'	 		=> 16,
				'quantity'			=> 17,
				'minimum'			=> 18,
				'subtract'			=> 20,
				'stock_status' 		=> 21,
				'shipping'			=> 22,
				'keyword'			=> 23,
				'image'				=> 19,
				'date_available'	=> 24,
				'length'			=> 25,
				'width'				=> 26,
				'height'			=> 27,
				'length_class'		=> 28,
				'weight'			=> 29,
				'weight_class'		=> 30,
				'status'			=> 15,
				'sort_order'		=> 31,
				'points'			=> 32,
				'manufacturer'		=> 33,
				'categories'		=> 34,
				'filters'			=> 35,
				'stores'			=> 36,
				'downloads'			=> 37,
				'related'			=> 38,
				'meta_title'			=> 39
		);
		
		// Extra fields
		$extras = array();
		foreach ($this->extraGeneralFields['Products'] as $extra) {
			if (!empty($extra['name']) && !empty($extra['column_light'])) {
				$extras[$extra['name']] = $extra['column_light'];
			}
		}
		
		$dataValidations = array(
				array(
						'type' => 'list',
						'field' => $generals['tax_class'],
						'data' => array($taxClassesStart[0], $taxClassesStart[1], $taxClassesStart[0], $taxClassesStart[1] + count($taxClasses) - 1),
						'range' => '',
						'count' => count($taxClasses)
				),
				array(
						'type' => 'list',
						'field' => $generals['subtract'],
						'data' => array(1,2,1,3),
						'range' => ''
				),
				array(
						'type' => 'list',
						'field' => $generals['stock_status'],
						'data' => array($stockStatesStart[0], $stockStatesStart[1], $stockStatesStart[0], $stockStatesStart[1] + count($stockStates) - 1),
						'range' => '',
						'count' => count($stockStates)
				),
				array(
						'type' => 'list',
						'field' => $generals['shipping'],
						'data' => array(1,2,1,3),
						'range' => ''
				),
				array(
						'type' => 'list',
						'field' => $generals['length_class'],
						'data' => array($lengthClassesStart[0], $lengthClassesStart[1], $lengthClassesStart[0], $lengthClassesStart[1] + count($lengthClasses) - 1),
						'range' => '',
						'count' => count($lengthClasses)
				),
				array(
						'type' => 'list',
						'field' => $generals['weight_class'],
						'data' => array($weightClassesStart[0], $weightClassesStart[1], $weightClassesStart[0], $weightClassesStart[1] + count($weightClasses) - 1),
						'range' => '',
						'count' => count($weightClasses)
				),
				array(
						'type' => 'list',
						'field' => $generals['status'],
						'data' => array(5,2,5,3),
						'range' => ''
				),
				array(
						'type' => 'list',
						'field' => $generals['manufacturer'],
						'data' => array($manufacturersStart[0], $manufacturersStart[1], $manufacturersStart[0], $manufacturersStart[1] + count($manufacturers) - 1),
						'range' => '',
						'count' => count($manufacturers)
				)
		);
		
		$target = array(0,2);
		
		$this->load->model('localisation/language');
		$languageQuery = $this->model_localisation_language->getLanguage($this->config->get('config_language_id'));
		
		$name = 'products_excelport_light_' . $languageQuery['code'] . '_' . str_replace('/', '_', substr(HTTP_CATALOG, 7, strlen(HTTP_CATALOG) - 8)) . '_' . date("Y-m-d_H-i-s") . '_' . $progress['current'];
		$resultName = $name . '.xlsx';
		$result = $destinationFolder . '/' . $name . '.xlsx';
		
		$objPHPExcel = PHPExcel_IOFactory::load($file);
		
		// Set document properties
		$objPHPExcel->getProperties()
		->setCreator($this->user->getUserName())
		->setLastModifiedBy($this->user->getUserName())
		->setTitle($name)
		->setSubject($name)
		->setDescription("Backup for Office 2007 and later, generated using PHPExcel and ExcelPort.")
		->setKeywords("office 2007 2010 2013 xlsx openxml php phpexcel excelport")
		->setCategory("Backup");
		
		$objPHPExcel->getDefaultStyle()->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		
		$metaSheetObj = $objPHPExcel->setActiveSheetIndex($metaSheet);
		
		for ($i = 0; $i < count($taxClasses); $i++) {
			$metaSheetObj->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($taxClassesStart[0]) . ($taxClassesStart[1] + $i), $taxClasses[$i]['title'], PHPExcel_Cell_DataType::TYPE_STRING);
		}
		for ($i = 0; $i < count($stockStates); $i++) {
			$metaSheetObj->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($stockStatesStart[0]) . ($stockStatesStart[1] + $i), $stockStates[$i]['name'], PHPExcel_Cell_DataType::TYPE_STRING);
		}
		for ($i = 0; $i < count($lengthClasses); $i++) {
			$metaSheetObj->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($lengthClassesStart[0]) . ($lengthClassesStart[1] + $i), $lengthClasses[$i]['title'], PHPExcel_Cell_DataType::TYPE_STRING);
		}
		for ($i = 0; $i < count($weightClasses); $i++) {
			$metaSheetObj->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($weightClassesStart[0]) . ($weightClassesStart[1] + $i), $weightClasses[$i]['title'], PHPExcel_Cell_DataType::TYPE_STRING);
		}
		for ($i = 0; $i < count($manufacturers); $i++) {
			$metaSheetObj->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($manufacturersStart[0]) . ($manufacturersStart[1] + $i), $manufacturers[$i]['name'], PHPExcel_Cell_DataType::TYPE_STRING);
		}
		for ($i = 0; $i < count($categories); $i++) {
			$metaSheetObj->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($categoriesStart[0]) . ($categoriesStart[1] + $i), $categories[$i]['category_id'], PHPExcel_Cell_DataType::TYPE_STRING);
			$metaSheetObj->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($categoriesStart[0] + 1) . ($categoriesStart[1] + $i), $categories[$i]['name'], PHPExcel_Cell_DataType::TYPE_STRING);
		}
		if (version_compare(VERSION, '1.5.5', '>=')) {
			for ($i = 0; $i < count($filters); $i++) {
				$metaSheetObj->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($filtersStart[0]) . ($filtersStart[1] + $i), $filters[$i]['filter_id'], PHPExcel_Cell_DataType::TYPE_STRING);
				$metaSheetObj->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($filtersStart[0] + 1) . ($filtersStart[1] + $i), $filters[$i]['group'] . $this->productFilterSeparator . $filters[$i]['name'], PHPExcel_Cell_DataType::TYPE_STRING);
			}
		}
		for ($i = 0; $i < count($stores); $i++) {
			$metaSheetObj->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($storesStart[0]) . ($storesStart[1] + $i), $stores[$i]['store_id'], PHPExcel_Cell_DataType::TYPE_STRING);
			$metaSheetObj->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($storesStart[0] + 1) . ($storesStart[1] + $i), $stores[$i]['name'], PHPExcel_Cell_DataType::TYPE_STRING);
		}
		for ($i = 0; $i < count($downloads); $i++) {
			$metaSheetObj->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($downloadsStart[0]) . ($downloadsStart[1] + $i), $downloads[$i]['download_id'], PHPExcel_Cell_DataType::TYPE_STRING);
			$metaSheetObj->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($downloadsStart[0] + 1) . ($downloadsStart[1] + $i), $downloads[$i]['name'], PHPExcel_Cell_DataType::TYPE_STRING);
		}
		
		$this->load->model('catalog/product');
		
		$extra_select = "";
		
		$this->db->query("SET SESSION group_concat_max_len = 1000000;");
		
		$products = $this->db->query($this->getQuery(array(), $store, $language, false, true) . " ORDER BY p.product_id "/*LIMIT ". $progress['current'] . ", " . $productNumber*/);
		
		$productSheetObj = $objPHPExcel->setActiveSheetIndex($productsSheet);
		
		foreach ($this->extraGeneralFields['Products'] as $extra) {
			if (!empty($extra['title']) && !empty($extra['column_light'])) {
				$productSheetObj->setCellValueExplicit($extra['column_light'] . '1', $extra['title'], PHPExcel_Cell_DataType::TYPE_STRING);
			}
		}
		
		if ($products->num_rows > 0) {
			foreach ($products->rows as $myProductIndex => $row) {
				
				$this->getData('Products', $row);
				$row['image']=$row['images'];
				// Prepare data
				foreach ($taxClasses as $taxClass) {
					if ($taxClass['tax_class_id'] == $row['tax_class_id']) { $row['tax_class'] = $taxClass['title']; break; }
				}
				if (empty($row['tax_class'])) $row['tax_class'] = $taxClasses[0]['title'];
				$row['subtract'] = empty($row['subtract']) ? 'No' : 'Yes';
				foreach ($stockStates as $stockStatus) {
					if ($stockStatus['stock_status_id'] == $row['stock_status_id']) { $row['stock_status'] = $stockStatus['name']; }
					//if ($stockStatus['stock_status_id'] == $this->config->get('config_stock_status_id')) { $defaultStockStatus = $stockStatus['name']; }
				}
				$defaultStockStatus = '';
				if (empty($defaultStockStatus) && !empty($stockStates[0]['name'])) {
					$defaultStockStatus = $stockStates[0]['name'];
				}
				if (empty($row['stock_status'])) $row['stock_status'] = $defaultStockStatus;
				$row['shipping'] = empty($row['shipping']) ? 'No' : 'Yes';
				$row['length_class'] = !empty($lengthClasses[0]['title']) ? $lengthClasses[0]['title'] : '';
				foreach ($lengthClasses as $lengthClass) {
					if ($lengthClass['length_class_id'] == $row['length_class_id']) { $row['length_class'] = $lengthClass['title']; break; }
				}
				$row['weight_class'] = !empty($weightClasses[0]['title']) ? $weightClasses[0]['title'] : '';
				foreach ($weightClasses as $weightClass) {
					if ($weightClass['weight_class_id'] == $row['weight_class_id']) { $row['weight_class'] = $weightClass['title']; break; }
				}
				$row['sort_order'] = empty($row['sort_order']) ? '0' : $row['sort_order'];
				$row['status'] = empty($row['status']) ? 'Disabled' : 'Enabled';
				foreach ($manufacturers as $manufacturer) {
					if ($manufacturer['manufacturer_id'] == $row['manufacturer_id']) { $row['manufacturer'] = $manufacturer['name']; break; }
				}
				if (empty($row['manufacturer'])) $row['manufacturer'] = $manufacturers[0]['name'];
				if (empty($row['filters'])) $row['filters'] = '';
				if (empty($row['ean'])) $row['ean'] = '';
				if (empty($row['jan'])) $row['jan'] = '';
				if (empty($row['isbn'])) $row['isbn'] = '';
				if (empty($row['mpn'])) $row['mpn'] = '';
				if (empty($row['name'])) $row['name'] = '-';
				$row['name'] = $this->entity_decode($row['name']);
				
				// Add data
				// Extras
				foreach ($extras as $name => $position) {
					$productSheetObj->setCellValueExplicit($position . ($target[1]), empty($row[$name]) ? '' : $row[$name], PHPExcel_Cell_DataType::TYPE_STRING);
				}
				//$generals['image']=41;
				// General
				foreach ($generals as $name => $position) {
					$productSheetObj->setCellValueExplicit(PHPExcel_Cell::stringFromColumnIndex($target[0] + $position) . ($target[1]), empty($row[$name]) && $row[$name] !== '0' ? '' : $row[$name], PHPExcel_Cell_DataType::TYPE_STRING);
				}
				
				// Data validations
				foreach ($dataValidations as $dataValidationIndex => $dataValidation) {
					if (isset($dataValidations[$dataValidationIndex]['count']) && $dataValidations[$dataValidationIndex]['count'] == 0) continue;
					$dataValidations[$dataValidationIndex]['range'] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $dataValidation['field']) . ($target[1]);
					if (empty($dataValidations[$dataValidationIndex]['root'])) $dataValidations[$dataValidationIndex]['root'] = PHPExcel_Cell::stringFromColumnIndex($target[0] + $dataValidation['field']) . ($target[1]);
				}
				
				$target[1] = $target[1] + 1;
				$progress['current']++;
				$progress['memory_get_usage'] = round(memory_get_usage(true)/(1024*1024));
				$progress['percent'] = 100 / ($products->num_rows / $progress['current']);
				
				//$this->setProgress($progress);
			}
			
			foreach ($dataValidations as $dataValidationIndex => $dataValidation) {
				if (isset($dataValidations[$dataValidationIndex]['count']) && $dataValidations[$dataValidationIndex]['count'] == 0) continue;
				if ($dataValidations[$dataValidationIndex]['range'] != $dataValidations[$dataValidationIndex]['root']) {
					$dataValidations[$dataValidationIndex]['range'] = $dataValidations[$dataValidationIndex]['root'] . ':' . $dataValidations[$dataValidationIndex]['range'];
				}
			}
			
			//Apply data validation for:
			// Generals
			foreach ($dataValidations as $dataValidation) {
				$range = trim($dataValidation['range']);
				if (isset($dataValidation['count']) && $dataValidation['count'] == 0) continue;
				if ($dataValidation['type'] == 'list' && !empty($dataValidation['root']) && !empty($range)) {
					$objValidation = $productSheetObj->getCell($dataValidation['root'])->getDataValidation();
					$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
					$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
					$objValidation->setAllowBlank(false);
					$objValidation->setShowInputMessage(true);
					$objValidation->setShowErrorMessage(true);
					$objValidation->setShowDropDown(true);
					$objValidation->setErrorTitle('Input error');
					$objValidation->setError('Value is not in list.');
					$objValidation->setPromptTitle('Pick from list');
					$objValidation->setPrompt('Please pick a value from the drop-down list.');
					$objValidation->setFormula1($metaSheetObj->getTitle() . '!$' . PHPExcel_Cell::stringFromColumnIndex($dataValidation['data'][0]) . '$' . ($dataValidation['data'][1]) . ':$' . PHPExcel_Cell::stringFromColumnIndex($dataValidation['data'][2]) . '$' . ($dataValidation['data'][3]));
					$productSheetObj->setDataValidation($range, $objValidation);
				}
			}
			
			unset($objValidation);
		} else {
			$progress['done'] = true;
		}
		
		$this->config->set('config_language_id', $default_language);
		
		$this->session->data['generated_file'] = $result;
		$this->session->data['generated_files'][] = $resultName;
		//$this->setProgress($progress);
		
		try {
			//$this->custom_set_time_limit();
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->setPreCalculateFormulas(false);
			
			$objWriter->save($result);
			
			$progress['done'] = true;
		} catch (Exception $e) {
			$progress['message'] = $e->getMessage();
			$progress['error'] = true;
			$progress['done'] = false;
			$this->setProgress($progress);
		}
		$objPHPExcel->disconnectWorksheets();
		unset($metaSheetObj);
		unset($objWriter);
		unset($productSheetObj);
		unset($objPHPExcel);
		
		$progress['done'] = true;
		//$this->setProgress($progress);
		
		return true;
	}

	public function importXLS($type='Products', $language=1, $file = 'c:\wamp\www\oc2\admin\tempmy\1.xlsx', $settings=array('ImportLimit'=>0), $addAsNew = false) {
		
		//TODO: Adaptiveimport_product-IMPORTXLSLIGHT
		$this->language->load('extension/module/excelimport');
		//if (!is_numeric($importLimit) || $importLimit < 10 || $importLimit > 800) throw new Exception($this->language->get('excelport_import_limit_invalid'));
		
		$default_language = $this->config->get('config_language_id');
		$this->config->set('config_language_id', $language);
		
		$progress = $this->getProgress();
		$progress['importedCount'] = !empty($progress['importedCount']) ? $progress['importedCount'] : 0;
		$progress['done'] = false;
		
		require_once(IMODULE_ROOT.'vendors/phpexcel/PHPExcel.php');
		// Create new PHPExcel object
		$importLimit=100;
		require_once(IMODULE_ROOT.'vendors/phpexcel/CustomReadFilter.php');
		$chunkFilter = new CustomReadFilter(array("Products" => array('A', ($progress['importedCount'] + 2), 'AN', (($progress['importedCount'] + $importLimit) + 1)), "products" => array('A', ($progress['importedCount'] + 2), 'AM', (($progress['importedCount'] + $importLimit) + 1))), true);
		
		$madeImports = false;
		$objReader = new PHPExcel_Reader_Excel2007();
		$objReader->setReadFilter($chunkFilter);
		$objReader->setReadDataOnly(true);
		$objReader->setLoadSheetsOnly(array("Products", "products"));
		$objPHPExcel = $objReader->load($file);
		$progress['importingFile'] = substr($file, strripos($file, '/') + 1);
		$productsSheet = 0;
		
		$productSheetObj = $objPHPExcel->setActiveSheetIndex($productsSheet);
		
		$progress['all'] = -1; //(int)(($productSheetObj->getHighestRow() - 2)/$this->productSize);
		//$this->setProgress($progress);
		
		$this->load->model('catalog/product');
		
		$map = array(
				'product_id' 		=> 0,
				'name'				=> 1,
				'meta_description'	=> 5,
				'meta_keyword'		=> 6,
				'description'		=> 4,
				'tag'				=> 7,
				'model' 			=> 2,
				'sku'				=> 3,
				'upc'				=> 8,
				'ean'				=> 9,
				'jan'				=> 10,
				'isbn'				=> 11,
				'mpn'				=> 12,
				'location'			=> 14,
				'price'				=> 13,
				'tax_class'	 		=> 16,
				'quantity'			=> 17,
				'minimum'			=> 18,
				'subtract'			=> 20,
				'stock_status' 		=> 21,
				'shipping'			=> 22,
				'keyword'			=> 23,
				'image'				=> 19,
				'date_available'	=> 24,
				'length'			=> 25,
				'width'				=> 26,
				'height'			=> 27,
				'length_class'		=> 28,
				'weight'			=> 29,
				'weight_class'		=> 30,
				'status'			=> 15,
				'sort_order'		=> 31,
				'points'			=> 32,
				'manufacturer'		=> 33,
				'categories'		=> 34,
				'filters'			=> 35,
				'stores'			=> 36,
				'downloads'			=> 37,
				'related'			=> 38,
				'meta_title'			=> 39
		);
		
		$source = array(0,2 + ($progress['importedCount']));
		
		$this->load->model('localisation/tax_class');
		$product_tax_classes = $this->model_localisation_tax_class->getTaxClasses();
		
		$this->load->model('localisation/stock_status');
		$product_stock_statusses = $this->model_localisation_stock_status->getStockStatuses();
		
		$this->load->model('localisation/length_class');
		$product_length_classes = $this->model_localisation_length_class->getLengthClasses();
		
		$this->load->model('localisation/weight_class');
		$product_weight_classes = $this->model_localisation_weight_class->getWeightClasses();
		
		$this->load->model('catalog/manufacturer');
		$product_manufacturers = $this->model_catalog_manufacturer->getManufacturers();
		
		$product_reward = array();
		
		if (version_compare(VERSION, '2.1.0.1', '>=')) {
			$this->load->model('customer/customer_group');
			$customer_groups = $this->model_customer_customer_group->getCustomerGroups();
		} else {
			$this->load->model('sale/customer_group');
			$customer_groups = $this->model_sale_customer_group->getCustomerGroups();
		}
		
		foreach ($customer_groups as $customer_group) {
			$product_reward[$customer_group['customer_group_id']] = array(
					'points' => ''
			);
		}
		
		$product_layout = array();
		$this->load->model('setting/store');
		$stores = array_merge(array(0 => array('store_id' => 0, 'name' => 'Default', 'url' => NULL, 'ssl' => NULL)),$this->model_setting_store->getStores());
		foreach ($stores as $store) {
			$product_layout[$store['store_id']] = array(
					'layout_id' => ''
			);
		}
		
		do {
			//$this->custom_set_time_limit();
			$product_name = strval($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['name']) . ($source[1]))->getValue());
			$product_name = $this->special_chars($product_name);
			if (!empty($product_name)) {
				$product_model = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['model']) . ($source[1]))->getValue();
				$product_id = $addAsNew ? '' : (int)trim($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['product_id']) . ($source[1]))->getValue());
				$product_price = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['price']) . ($source[1]))->getValue();
				$product_price = (float)str_replace(array(' ', ','), array('', '.'), $product_price);
				
				$found = false;
				foreach ($product_tax_classes as $product_tax_class) {
					if ($product_tax_class['title'] == $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['tax_class']) . ($source[1]))->getValue()) {
						$found = true;
						$product_tax_class_id = $product_tax_class['tax_class_id'];
						break;
					}
				}
				if (!$found) $product_tax_class_id = 0;
				
				$product_quantity = (int)str_replace(' ', '', $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['quantity']) . ($source[1]))->getValue());
				$product_minimum = (int)str_replace(' ', '', $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['minimum']) . ($source[1]))->getValue());
				$product_subtract = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['subtract']) . ($source[1]))->getValue() == 'Yes' ? 1 : 0;
				
				$found = false;
				foreach ($product_stock_statusses as $product_stock_status) {
					if ($product_stock_status['name'] == $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['stock_status']) . ($source[1]))->getValue()) {
						$found = true;
						$product_stock_status_id = $product_stock_status['stock_status_id'];
						break;
					}
				}
				if (!$found) $product_stock_status_id = 0;
				
				$product_shipping = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['shipping']) . ($source[1]))->getValue() == 'Yes' ? 1 : 0;
				$product_length = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['length']) . ($source[1]))->getValue();
				$product_length = (float)str_replace(array(' ', ','), array('', '.'), $product_length);
				$product_width = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['width']) . ($source[1]))->getValue();
				$product_width = (float)str_replace(array(' ', ','), array('', '.'), $product_width);
				$product_height = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['height']) . ($source[1]))->getValue();
				$product_height = (float)str_replace(array(' ', ','), array('', '.'), $product_height);
				
				$found = false;
				foreach ($product_length_classes as $product_length_class) {
					if ($product_length_class['title'] == $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['length_class']) . ($source[1]))->getValue()) {
						$found = true;
						$product_length_class_id = $product_length_class['length_class_id'];
						break;
					}
				}
				if (!$found) $product_length_class_id = 0;
				
				$product_weight = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['weight']) . ($source[1]))->getValue();
				$product_weight = (float)str_replace(array(' ', ','), array('', '.'), $product_weight);
				
				$found = false;
				foreach ($product_weight_classes as $product_weight_class) {
					if ($product_weight_class['title'] == $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['weight_class']) . ($source[1]))->getValue()) {
						$found = true;
						$product_weight_class_id = $product_weight_class['weight_class_id'];
						break;
					}
				}
				if (!$found) $product_weight_class_id = 0;
				
				$product_status = $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['status']) . ($source[1]))->getValue() == 'Enabled' ? 1 : 0;
				$product_sort_order = (int)str_replace(' ', '', $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['sort_order']) . ($source[1]))->getValue());
				
				$found = false;
				foreach ($product_manufacturers as $product_manufacturer) {
					if ($product_manufacturer['name'] == $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['manufacturer']) . ($source[1]))->getValue()) {
						$found = true;
						$product_manufacturer_id = $product_manufacturer['manufacturer_id'];
						break;
					}
				}
				if (!$found) $product_manufacturer_id = 0;
				
				$product_store = array();
				$product_stores = explode(',', str_replace('.', ',', strval($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['stores']) . ($source[1]))->getValue())));
				foreach ($product_stores as $store) {
					$store = trim($store);
					if ($store !== '') $product_store[] = $store;
				}
				
				$product_category = array();
				$categories = explode(',', str_replace('.', ',', strval($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['categories']) . ($source[1]))->getValue())));
				foreach ($categories as $category) {
					$category = trim($category);
					if (!empty($category)) $product_category[] = trim($category);
				}
				
				$product_filter = array();
				if (version_compare(VERSION, '1.5.5', '>=')) {
					$filters = explode(',', str_replace('.', ',', strval($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['filters']) . ($source[1]))->getValue())));
					foreach ($filters as $filter) {
						$filter = trim($filter);
						if (!empty($filter)) $product_filter[] = trim($filter);
					}
				}
				
				$product_download = array();
				$downloads = explode(',', str_replace('.', ',', strval($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['downloads']) . ($source[1]))->getValue())));
				foreach ($downloads as $download) {
					$download = trim($download);
					if (!empty($download)) $product_download[] = trim($download);
				}
				
				$product_related = array();
				$related = explode(',', str_replace('.', ',', strval($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['related']) . ($source[1]))->getValue())));
				foreach ($related as $relate) {
					$relate = trim($relate);
					if (!empty($relate)) $product_related[] = trim($relate);
				}
				
				$product = array(
						'product_description' => array(
								$language => array(
										'name' => $product_name,
										'meta_description' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['meta_description']) . ($source[1]))->getValue(),
										'meta_keyword' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['meta_keyword']) . ($source[1]))->getValue(),
										'meta_title' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['meta_title']) . ($source[1]))->getValue(),
										'description' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['description']) . ($source[1]))->getValue(),
										'tag' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['tag']) . ($source[1]))->getValue(),
								)
						),
						'model' => $product_model,
						'sku' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['sku']) . ($source[1]))->getValue(),
						'upc' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['upc']) . ($source[1]))->getValue(),
						'ean' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['ean']) . ($source[1]))->getValue(),
						'jan' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['jan']) . ($source[1]))->getValue(),
						'isbn' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['isbn']) . ($source[1]))->getValue(),
						'mpn' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['mpn']) . ($source[1]))->getValue(),
						'location' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['location']) . ($source[1]))->getValue(),
						'price' => $product_price,
						'tax_class_id' => $product_tax_class_id,
						'quantity' => trim($product_quantity),
						'minimum' => trim($product_minimum),
						'subtract' => $product_subtract,
						'stock_status_id' => $product_stock_status_id,
						'shipping' => $product_shipping,
						'keyword' => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['keyword']) . ($source[1]))->getValue(),
						'image' => trim($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['image']) . ($source[1]))->getValue()),
						'date_available' => trim($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['date_available']) . ($source[1]))->getValue()),
						'length' => $product_length,
						'width' => $product_width,
						'height' => $product_height,
						'length_class_id' => $product_length_class_id,
						'weight' => $product_weight,
						'weight_class_id' => $product_weight_class_id,
						'status' => $product_status,
						'sort_order' => $product_sort_order,
						'manufacturer_id' => $product_manufacturer_id,
						'product_category' => $product_category,
						'product_filter' => $product_filter,
						'product_store' => $product_store,
						'product_download' => $product_download,
						'related' => '',
						'product_related' => $product_related,
						'option' => '',
						'points' => (int)trim($productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['points']) . ($source[1]))->getValue()),
						'product_reward' => $product_reward,
						'product_layout' => $product_layout
				);
				
				if (version_compare(VERSION, '1.5.4', '<')) {
					$product['product_tag'] = array(
							$language => $productSheetObj->getCell(PHPExcel_Cell::stringFromColumnIndex($source[0] + $map['tag']) . ($source[1]))->getValue()
					);
				}
				
				// Extras
				foreach ($this->extraGeneralFields['Products'] as $extra) {
					if (!empty($extra['name']) && !empty($extra['column_light'])) {
						$product[$extra['name']] = $productSheetObj->getCell($extra['column_light'] . $source[1])->getValue();
					}
				}
				
				if (!empty($product_id)) {
					$exists = false;
					$existsQuery = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE product_id = ".$product_id);
					
					$exists = $existsQuery->num_rows > 0;
					
					if ($exists) {
						$this->editProduct($product_id, $product, $allLanguages, true);
					} else {
						$this->addProduct($product_id, $product, $allLanguages);
					}
				} else {
					$this->addProduct('', $product, $allLanguages);
				}
				
				$progress['current']++;
				$progress['importedCount']++;
				$madeImports = true;
				//$this->setProgress($progress);
			}
			$source[1] += 1;
		} while (!empty($product_name));
		$progress['done'] = true;
		if (!$madeImports) {
			$progress['importedCount'] = 0;
			array_shift($this->session->data['uploaded_files']);
		}
		//$this->setProgress($progress);
		
		$this->config->set('config_language_id', $default_language);
	}
	
}
