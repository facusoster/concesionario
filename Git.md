## Inicia el repositorio local:

```bash
facus@KameHouse MINGW64 /d/xampp/htdocs/Soster_Facundo_ProdWeb1
$ git init
```

## Añade tus archivos al área de preparación:

```bash
facus@KameHouse MINGW64 /d/xampp/htdocs/Soster_Facundo_ProdWeb1
$ git init
```

## Crea tu primer punto de control (commit):

```bash
facus@KameHouse MINGW64 /d/xampp/htdocs/Soster_Facundo_ProdWeb1 (master)
$ git commit -m "Subiendo mi proyecto PHP"
```

## Vincula con GitHub:

Ve a tu repositorio en GitHub, copia la URL y pégala aquí:

```bash
facus@KameHouse MINGW64 /d/xampp/htdocs/Soster_Facundo_ProdWeb1 (master)
$ git remote add origin https://github.com/facusoster/produccionWeb01
```

## Sube los archivos:

```bash
facus@KameHouse MINGW64 /d/xampp/htdocs/Soster_Facundo_ProdWeb1 (master)
$ git push -u origin master
```

# Actualizar Repositorio

## Preparar los cambios: 
Dile a Git qué archivos nuevos o modificados quieres subir.

```bash
git add .
```

> (El punto significa "todos los archivos". Si solo quieres uno específico, usa git add nombre-del-archivo.php).

## Etiquetar los cambios: 
Ponle un nombre a lo que hiciste para identificarlo luego.

```bash
git commit -m "Explica brevemente qué cambiaste"
```

> (Ejemplo: git commit -m "Corrección en el controlador de usuarios").

## Subir a GitHub: 
Envía tus cambios locales a la nube.

```bash
git push
```
# Descarga Repositorio

## Obtener la URL del repositorio
```Bash
https://github.com/facusoster/produccionWeb01
```

## Clonar el proyecto
Abre una terminal (Git Bash o CMD) en la carpeta donde quieras guardar el proyecto y escribe:

```bash
git clone https://github.com/facusoster/produccionWeb01
```

# ¿Cómo bajar cambios nuevos? (Si ya lo habías clonado antes)

Si ya tienes el proyecto en la segunda PC y quieres traer las actualizaciones que hiciste en la primera, solo entra a la carpeta desde la terminal y usa:

```bash
git pull
```