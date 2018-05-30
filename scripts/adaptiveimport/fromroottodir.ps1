Copy-Item ../../admin/controller/extension/module/adaptiveimport.php  -destination installed\admin/controller/extension/module/ -Force -Recurse
Copy-Item ../../admin/language/en-gb/extension/module/adaptiveimport.php  -destination installed\admin/language/en-gb/extension/module/ -Force -Recurse
Copy-Item ../../admin/language/ru-ru/extension/module/adaptiveimport.php  -destination installed\admin/language/ru-ru/extension/module/ -Force -Recurse
Copy-Item ../../admin/model/extension/module/  -destination installed\admin/model/extension/module/ -Force -Recurse
Copy-Item ../../admin/view/stylesheet/adaptiveimport.css  -destination installed\admin/view/stylesheet/ -Force -Recurse
Copy-Item ../../admin/view/template/extension/module/adaptiveimport.tpl  -destination installed\admin/view/template/extension/module/ -Force -Recurse
Copy-Item ../../vendors  -destination installed\vendors -Force -Recurse
Copy-Item ../../system/excelportdb.php  -destination installed\system/ -Force -Recurse


git add ../../scripts
git commit -m "frombat"
