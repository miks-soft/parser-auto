# EV Parser
Parser collects cars from websites

### Configuration
Install in `.env`
```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ev_parser
DB_USERNAME=root
DB_PASSWORD=
```

### Install
```
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
chmod o+w -R storage bootstrap/cache public
```

### Cron
```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Proxy
The proxy list is in file `proxies.txt`. To update the proxy list, run the command
```
php artisan proxy:update
```

### User-Agent
The user-agent list is in the file `user-agents.txt`. To update the list of agents, run the command
```
php artisan user-agent:update
```

### Schedule
| Action | Day | Time |
| ------ | ------ | ------ |
| Update exchange rates | every day | every hour |

To actually view the schedule, run
```
php artisan schedule:list
```

### Teams
| Team | Action |
| ------ | ------ |
| php artisan orchid:admin {name} {email} {password} | Create an administrator  |
| php artisan currency:parse | Update exchange rates |
| php artisan cars:parse {source} | Start parsing |
| php artisan cars:parse-now | Run all parsers in the background |
