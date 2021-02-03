PHP test

### Installation
- clone this application from the git repository
- use your local PHP installation or use the provided docker-composer.yaml to build the docker container.  
- run `composer install` locally or from the docker container, depending on what you've chosen to use in the previous step
- in case of using local PHP installation run  `php artisan serve` from the app root folder
- in case of using provided docker-compose.yaml the application is going to be available by entering `localhost:8080` in your browser. The cli in this case will be accessible by entering the container - `docker exec -ti container_name sh` (replace `container_name` with the actual name). 


### DB connection
Use `.env` config file to configure the application to use your existing database.

In case if the sqlite is the database of your choice use the following steps:

- run `touch /path/to/your/app/folder/database/database.sqlite`
- in `.env` change to following lines:
    - `DB_CONNECTION=sqlite`
    - `DB_DATABASE=/path/to/your/app/folder/database/database.sqlite`
    
### Scheduled tasks
In order to enable scheduled task make sure to add the main scheduler to your crontab

Run `crontab -e` and add the following line
 
`* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1`

*Be sure to replace `/path/to/artisan` with the absolute path to the artisan file*
