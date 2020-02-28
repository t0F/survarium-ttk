# survarium-ttk
Demo https://pi4.freeboxos.fr/

Survarium Stats - Symfony Php 5 app to extract and display weapons data from game.json Survarium file. 
Currently able to calculate and display Time to kill for each weapons on each armor set piece.

How to use : 
- Go to https://pi4.freeboxos.fr/
- Clic on table headers to short the table
- You can filter weapon types in table footer.
- You can use the search function for a specific weapon.
- You can change target armor and more under the table. It doesn't reset filters in array. 

Next update will probably add one (or more) of thoses features : 
- a page to show hidden stats from weapon. 
- a page to compare time to kill on all sets in a table, by selected a weapon. 
- a more visual page to let users selects every armor of them set, then select a weapon to show damages.
- a dark them, the target is the current render with hacker vision Chrome extension  (https://pi4.freeboxos.fr/assets/img/survariumStatsHV.jpg )
- attachments customisation
- translations
- ...

How to install extract part on a server : 
- install a php environmnent and a database
- edit database url in .env file (you don't have to create schema)
```bash
php bin/console d:d:c
php bin/console d:s:u --force
php bin/console app:import
```
It will create three tables : equipment, gearget, and weapon. At current version only usefull stats for the time to kill comparator page is available. 

If you want a fresh game.json file, you need to extract db files to json, using quickbms then a nodejs tool available on survarium forum. (TODO:add link). You can change game.json path in src\Command\ImportOptionsCommand.php, first line of execute.

If you want to install the time to kill webpage, you need to install webpack, launch it and install every missing dependencies with yarn. 
You may need to edit publicPath in webpack.config.php depending of public url. 

