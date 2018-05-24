$modulnem = "helloworld"
$modulvar = "Helloworld"
New-Item -Force catalog\view\theme\default\template\extension\module\$modulnem.tpl -type file
New-Item -Force catalog\controller\extension\module\$modulnem.php -type file
New-Item -Force catalog\language\ru-ru\extension\module\$modulnem.php -type file
New-Item -Force admin\view\template\extension\module\$modulnem.tpl -type file
New-Item -Force admin\controller\extension\module\$modulnem.php -type file
New-Item -Force admin\language\ru-ru\extension\module\$modulnem.php -type file

$catlang = '<?php
$_[''heading_title''] = ''Заголовок модуля'';'

$catcontroller = '<?php  
class ControllerExtensionModule'+$modulvar+' extends Controller {
	public function index() {

		$this->load->language(''extension/module/'+$modulvar+'''); //подключаем любой языковой файл
		$data[''heading_title''] = $this->language->get(''heading_title''); //объявляем переменную heading_title с данными из языкового файла

		$data[''content'']="Ваш контент";        //можно задать данные, сразу в контроллере

		$this->load->model(''catalog/product''); //подключаем любую модель из OpenCart

		$data[''product_info'']=$this->model_catalog_product->getProduct(42); //используем метод подключенной модели, например getProduct(42) – информация о продукте id  42

		//стандартная процедура для контроллеров OpenCart, выбираем файл представления модуля для вывода данных
		if (file_exists(DIR_TEMPLATE . $this->config->get(''config_template'') . ''/template/extension/module/'+$modulnem+'.tpl'')) {
			return $this->load->view($this->config->get(''config_template'') . ''/template/extension/module/'+$modulnem+'.tpl'', $data);
		} else {
			return $this->load->view(''default/template/extension/module/'+$modulnem+'.tpl'', $data);
		}		

	}
}?>'
$catview = '<?php echo $heading_title; ?>
<br>
<?php echo $content; ?>
<br>
<?php echo $product_info[''name'']; ?>'

$admlang = '<?php
// Имя модуля, такой он будет виден на вкладке «модули» в панели управления и внутри настроек модуля
$_[''heading_title'']    = '''+$modulnem+''';

//Текст внутри настроек модуля
$_[''text_module'']      = ''Модули'';
$_[''text_edit'']        = ''Настройки модуля'';
$_[''entry_status'']     = ''Статус'';'
$admcontroller = '<?php
class ControllerExtensionModule'+$modulvar+' extends Controller {
	private $error = array();

	public function index() {
		$this->load->language(''extension/module/'+$modulnem+'''); //подключаем наш языковой файл

		$this->load->model(''setting/setting'');   //подключаем модель setting, он позволяет сохранять настройки модуля в БД

		if (($this->request->server[''REQUEST_METHOD''] == ''POST'') && $this->validate()) { //если мы нажали "Сохранить"  в панели, мы сохраняем текущие настройки
			$this->model_setting_setting->editSetting('''+$modulnem+''', $this->request->post);
			$this->response->redirect($this->url->link(''extension/module'', ''token='' . $this->session->data[''token''], ''SSL''));
		}

         // ваши переменные
		$data[''heading_title''] = $this->language->get(''heading_title'');
		$data[''text_edit''] = $this->language->get(''text_edit'');
		$data[''text_enabled''] = $this->language->get(''text_enabled'');
		$data[''text_disabled''] = $this->language->get(''text_disabled'');		

		$data[''entry_status''] = $this->language->get(''entry_status'');

        // если метод validate вернул warning, передадим его представлению
		if (isset($this->error[''warning''])) {
			$data[''error_warning''] = $this->error[''warning''];
		} else {
			$data[''error_warning''] = '''';
		}

        // далее идет формирование массива breadcrumbs (хлебные крошки)
		$data[''breadcrumbs''] = array();
		$data[''breadcrumbs''][] = array(
			''text'' => $this->language->get(''text_home''),
			''href'' => $this->url->link(''common/dashboard'', ''token='' . $this->session->data[''token''], ''SSL'')
		);
		$data[''breadcrumbs''][] = array(
			''text'' => $this->language->get(''text_module''),
			''href'' => $this->url->link(''extension/module'', ''token='' . $this->session->data[''token''], ''SSL'')
		);
		$data[''breadcrumbs''][] = array(
			''text'' => $this->language->get(''heading_title''),
			''href'' => $this->url->link(''module/category'', ''token='' . $this->session->data[''token''], ''SSL'')
		);

        //ссылки для формы и кнопки "cancel"
		$data[''action''] = $this->url->link(''extension/module/'+$modulnem+''', ''token='' . $this->session->data[''token''], ''SSL'');
		$data[''cancel''] = $this->url->link(''extension/module/'+$modulnem+''', ''token='' . $this->session->data[''token''], ''SSL'');

		//переменная с статусом модуля
        if (isset($this->request->post['''+$modulnem+'_status''])) {
			$data[''mymodul_status''] = $this->request->post['''+$modulnem+'_status''];
		} else {
			$data[''mymodul_status''] = $this->config->get('''+$modulnem+'_status'');
		}

        //ссылки на контроллеры header,column_left,footer, иначе мы не сможем вывести заголовок, подвал и левое меню в файле представления
		$data[''header''] = $this->load->controller(''common/header'');
		$data[''column_left''] = $this->load->controller(''common/column_left'');
		$data[''footer''] = $this->load->controller(''common/footer'');

        //в качестве файла представления модуля для панели администратора использовать файл mymodul.tpl
		$this->response->setOutput($this->load->view(''extension/module/'+$modulnem+'.tpl'', $data));
	}

    //обязательный метод в контроллере, он запускается для проверки разрешено ли пользователю изменять настройки данного модуля
	protected function validate() {
		if (!$this->user->hasPermission(''modify'', ''module/category'')) {
			$this->error[''warning''] = $this->language->get(''error_permission'');
		}
		return !$this->error;
	}
}'
$admview = '<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-mymodul" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb[''href'']; ?>"><?php echo $breadcrumb[''text'']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-mymodul" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="mymodul_status" id="input-status" class="form-control">
                <?php if ($mymodul_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>'

$catlang | Out-File -Filepath catalog\language\ru-ru\extension\module\$modulnem.php -Encoding UTF8
$catcontroller | Out-File -Filepath catalog\controller\extension\module\$modulnem.php -Encoding UTF8
$catview | Out-File -Filepath catalog\view\theme\default\template\extension\module\$modulnem.tpl -Encoding UTF8
$admlang | Out-File -Filepath admin\language\ru-ru\extension\module\$modulnem.php -Encoding UTF8
$admcontroller | Out-File -Filepath admin\controller\extension\module\$modulnem.php -Encoding UTF8
$admview | Out-File -Filepath admin\view\template\extension\module\$modulnem.tpl -Encoding UTF8

$uninstnstscript = '
Remove-Item -Path ..\..\admin\controller\extension\module\'+$modulnem+'.php 
Remove-Item -Path ..\..\admin\language\ru-ru\extension\module\'+$modulnem+'.php 
Remove-Item -Path ..\..\admin\view\template\extension\module\'+$modulnem+'.tpl

Remove-Item -Path ..\..\catalog\controller\extension\module\'+$modulnem+'.php 
Remove-Item -Path ..\..\catalog\language\ru-ru\extension\module\'+$modulnem+'.php 
Remove-Item -Path ..\..\catalog\view\theme\default\template\extension\module\'+$modulnem+'.tpl
'

$uninstnstscript | Out-File -Filepath uninstall.ps1 -Encoding ASCII
