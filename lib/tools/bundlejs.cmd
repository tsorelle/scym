@echo off
echo Adding Tops.Peanut files
java -jar lib/tools/bin/yuicompressor-2.4.8.jar assets\js\Tops.Peanut\*.js >  assets\scripts\topsjs.min.js
echo Adding Tops.App files
java -jar lib/tools/bin/yuicompressor-2.4.8.jar assets\js\Tops.App\*.js >>  assets\scripts\topsjs.min.js
echo done

