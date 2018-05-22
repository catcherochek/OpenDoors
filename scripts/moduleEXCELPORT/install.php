<?php

// ExcelPort OCMOD installer. Created by iSenseLabs - http://isenselabs.com

$this->load->model('extension/modification');

if (VERSION == '2.0.0.0') {
    $excelport_install_dir = DIR_DOWNLOAD . str_replace(array('../', '..\\', '..'), '', $this->request->post['path']);
} else {
    $excelport_install_dir = DIR_UPLOAD . str_replace(array('../', '..\\', '..'), '', $this->request->post['path']);
}

$excelport_ocmod_dir = $excelport_install_dir . '/_ocmod';

$excelport_ocmod_files = scandir($excelport_ocmod_dir);

foreach ($excelport_ocmod_files as $excelport_ocmod_file) {
    if (in_array($excelport_ocmod_file, array('.', '..'))) continue;

    $excelport_xml_file = $excelport_ocmod_dir . DIRECTORY_SEPARATOR . $excelport_ocmod_file;
    $excelport_xml_contents = file_get_contents($excelport_xml_file);

    if (method_exists($this->model_extension_modification, 'getModificationByCode')) {
        $excelport_dom = new DOMDocument('1.0', 'UTF-8');
        $excelport_dom->loadXml($excelport_xml_contents);

        $excelport_code_item = $excelport_dom->getElementsByTagName('code')->item(0);

        if ($excelport_code_item) {
            $excelport_code = $excelport_code_item->nodeValue;

            $excelport_modification = $this->model_extension_modification->getModificationByCode($excelport_code);

            if (!empty($excelport_modification)) {
                $this->model_extension_modification->deleteModification($excelport_modification['modification_id']);
            }
        }
    }

    file_put_contents($excelport_install_dir . '/install.xml', $excelport_xml_contents);

    $this->xml();
}