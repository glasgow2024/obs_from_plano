# OBS from Planorama

Simple script to read the Planorama JSON data extract intended for ConClár, and
write an individual file per room for the OBS system.

## Usage

After extracting the repository:

1. Copy `settings.example.php` to `settings.php`.
2. Edit `settings.php` to edit file locations. `PROG_FILE` and `PEOPLE_FILE` are
   the paths to the data files. These can be external URLs, or local file paths.
   If using local file paths, it is recommended to use the absolute path,
   especially if running the script as a cron task.
3. Ensure the user running the script has write access to `/web` directory.
4. It is recommended that the webroot be set to the `/web` directory, and keep
   the PHP scripts private.
5. The script can be run with `php obs_from_plano.php`.
6. To schedule a cron task, use `crontab -e` and add a line for the job:

```0,15,30,45 * * * * php /path/to/obs_from_plano.php```

## Author

Developed by James Shields (lostcarpark) and released under the MIT licence.
