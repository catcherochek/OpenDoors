(Write-Host "!!!!!Скрипт создающий модуль helloworld в " -ForegroundColor "Yellow" -NoNewLine);  (Write-Host "OPENCART "-ForegroundColor "Blue");
(Write-Host "!!!!!Создается 6 файла в двух ветках(язык, контроллер, и View(отображение), без описания модели.)");
(Write-Host "!!!!!Нажмите   " -ForegroundColor "White" -NoNewLine); (Write-Host "[ЕНТЭР]" -ForegroundColor "RED" -NoNewLine);(Write-Host " и мы приступим" -ForegroundColor "White");
$null = $Host.UI.RawUI.ReadKey('NoEcho,IncludeKeyDown');


Copy-Item admin  -destination ..\.. -Force -Recurse
Copy-Item catalog  -destination ..\.. -Force -Recurse


(Write-Host "!!!!!СДЕЛАНО!!!, для удаления модуля запустите скрипт UNINSTAL.PS1 " -ForegroundColor "White" -NoNewLine);
$null = $Host.UI.RawUI.ReadKey('NoEcho,IncludeKeyDown');