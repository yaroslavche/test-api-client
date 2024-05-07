## API Client

Symfony 7, PHP 8

### Run
```shell
$ make build
$ make up
$ make install
```

Note: if make command isn't currently installed on your system, look through [Makefile](Makefile) actual docker commands.

Note: if you want to redefine API Server host URL, you need to change `API_SERVER_HOST` in [.env](.env)

## Commands

### Report

Get report with the list of users of each group:
```shell
docker compose exec api_client_php bin/console app:report:group-users
```
Get report with the list of users for specific group-identifier:
```shell
docker compose exec api_client_php bin/console app:report:group-users <group-identifier>
```

### Group
List all groups:
```shell
docker compose exec api_client_php bin/console app:group:list
```
Create group with <group-name> (required):
```shell
docker compose exec api_client_php bin/console app:group:create <group-name>
```
Read group by <group-identifier> (required):
```shell
docker compose exec api_client_php bin/console app:group:read <group-identifier>
```
Change <group-name> (required) by <group-identifier> (required):
```shell
docker compose exec api_client_php bin/console app:group:change-name <group-identifier> <group-name>
```
Delete group by <group-identifier> (required):
```shell
docker compose exec api_client_php bin/console app:group:delete <group-identifier>
```

### User
List all users:
```shell
docker compose exec api_client_php bin/console app:user:list
```
Create user with <user-email> (required) and <user-name> (required):
```shell
docker compose exec api_client_php bin/console app:user:create <user-email> <user-name>
```
Read user by <user-identifier> (required):
```shell
docker compose exec api_client_php bin/console app:user:read <user-identifier>
```
Change <user-name> (required) by <user-identifier> (required):
```shell
docker compose exec api_client_php bin/console app:user:change-name <user-identifier> <user-name>
```
Delete user by <user-identifier> (required):
```shell
docker compose exec api_client_php bin/console app:user:delete <user-identifier>
```
Assign user by <user-identifier> (required) to a group by <group-identifier> (required):
```shell
docker compose exec api_client_php bin/console app:user:assign-to-group <user-identifier> <group-identifier>
```
Remove user by <user-identifier> (required) from a group by <group-identifier> (required):
```shell
docker compose exec api_client_php bin/console app:user:remove-from-group <user-identifier> <group-identifier>
```
