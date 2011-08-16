@echo off

REM This script will only execute on production Windows Azure. The PS script prohibits running on devfabric.

ECHO Installing PHP runtime... >> ..\startup-tasks-log.txt

powershell.exe Set-ExecutionPolicy Unrestricted
powershell.exe .\install-php.ps1

ECHO Installed PHP runtime. >> ..\startup-tasks-log.txt