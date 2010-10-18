@echo off

SET args=%*
if "%args%" == "" goto usage

wget -nv -O NUL -o NUL http://blackbox:8080/web_collector_parse.php?a=%args%
goto end

:usage
echo Usage: %0 event:eventName;key1:value1;key2:value2
goto end

:end
