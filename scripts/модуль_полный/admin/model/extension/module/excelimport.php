<?php
class ModelExtensionModuleExcelimport extends Model {
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
		
		$this->conditions['Products']['general_product_tags'] = array(
				'label' => 'Product Tags',
				'join_table' => version_compare(VERSION, '1.5.4', '<') ? 'product_tag' : 'product_description',
				'field_name' => version_compare(VERSION, '1.5.4', '<') ? 'pt.tag' : 'pd.tag',
				'type' => 'text'
		);
		
		$this->conditions['Customers']['customer_customer_group'] = array(
				'label' => 'Customer Group Name',
				'join_table' => version_compare(VERSION, '1.5.3', '<') ? 'customer_group' : 'customer_group_description',
				'field_name' => version_compare(VERSION, '1.5.3', '<') ? 'cg.name' : 'cgd.name',
				'type' => 'text'
		);
		
		$this->conditions['CustomerGroups']['customer_group_name'] = array(
				'label' => 'Customer Group Name',
				'join_table' => version_compare(VERSION, '1.5.3', '<') ? NULL : 'customer_group_description',
				'field_name' => version_compare(VERSION, '1.5.3', '<') ? 'cg.name' : 'cgd.name',
				'type' => 'text'
		);
		
		parent::__construct($register);
	}
	public function exportXLS($type='Products', $language=1, $store=0, $destinationFolder = 'tempmy', $settings=array('ExportLimit'=>800), $quickExport = true, $filter = false, $filters = array()) {
		$i=5;
	}
	
}
