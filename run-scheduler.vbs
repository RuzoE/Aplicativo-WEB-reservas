Set WinScriptHost = CreateObject("WScript.Shell")
' Ejecuta el scheduler de Laravel de forma silenciosa (parámetro 0 oculta la ventana)
WinScriptHost.Run "php c:\laragon\www\hotel-piloto-sam\artisan schedule:run", 0
Set WinScriptHost = Nothing
