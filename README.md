# Survarium Stats
Time to kill Calculator - php symfony 5 application. 

## Demo 
https://pi4.freeboxos.fr/ 

## Usage : 
- Clic on table headers to short the table
- You can filter weapon types in table footer.
- You can use the search function for a specific weapon.
- You can change target armor and more under the table. It doesn't reset filters in array. 

## Todo
- a page to show hidden stats from weapon. 
- a page to compare time to kill on all sets in a table, by selected a weapon. 
- a more visual page to let users selects every armors of a full build, then select / customize a weapon to show damages.
- a dark theme, the visual target is the current render with hacker vision Chrome extension  (https://pi4.freeboxos.fr/assets/img/survariumStatsHV.jpg ).
- attachments customisation
- translations
- ...

Any suggestions / bug reports are welcome ! Contact me here or in survarium discord.
Also feel free to help on dev, just contact me for installation support and repository rights. Both PHP and Symfony have very helpfull documentations. 

## Installation
- install a php environment with a database (eg : xamp on windows) You don't have to create schema database, it will create it.
- clone repository
- edit database url in .env file to suit your installation
```bash
php bin/console d:d:c
php bin/console d:s:u --force
php bin/console app:import
```
It will create three tables : equipment, gearset, and weapon. At current version only usefull stats for the time to kill comparator page, (and few bonus weapons stats) are extracted. 

If you want a fresh game.json file, you need to extract db files to json, using quickbms then a nodejs tool available on survarium forum. (TODO:add link to topic).

If you want to install the time to kill webpage, you need to install webpack, launch it and install every missing dependencies with yarn until it complete build. You may need to edit publicPath in webpack.config.php depending of public url. 

