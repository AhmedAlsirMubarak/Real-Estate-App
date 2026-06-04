$ErrorActionPreference = 'Stop'

$mysqldump = 'C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqldump.exe'
$mysql = 'C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe'
$dumpPath = 'C:\laragon\backup\mysql\pre_restore_2026-06-04.sql'
$backupFile = 'C:\laragon\backup\mysql\mysql-8.4-2026-06-04_002719.sql'

Write-Output "Using mysqldump: $mysqldump"
Write-Output "Using mysql: $mysql"

Write-Output "Dumping current DB to $dumpPath"
& $mysqldump -u root real_estate_app > $dumpPath
if ($LASTEXITCODE -ne 0) { Write-Error "Dump failed with exit code $LASTEXITCODE"; exit 1 }

Write-Output "Dump created: $dumpPath"

Write-Output "Importing backup $backupFile into real_estate_app"
$proc = Start-Process -FilePath $mysql -ArgumentList ('-u','root','real_estate_app') -RedirectStandardInput $backupFile -NoNewWindow -Wait -PassThru
Write-Output "Import finished with exitcode $($proc.ExitCode)"
exit $proc.ExitCode
