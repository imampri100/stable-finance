# stable-finance
> - Stable Finance is a PHP-based web application designed to help users efficiently manage personal or small business finances. 
> - It provides key features such as daily income and expense tracking, category-based financial organization, and simple financial reports with charts and monthly summaries. 
> - With its lightweight and responsive interface, Stable Finance makes it easy for users to monitor their financial stability anytime through a web browser.

## Development

### Running app in development
```bash
make dev-up
```

### Running app in development with no cache
#### Command:
```bash
make dev-up-clean
```

#### Web running on: 
> http://localhost:8080

#### Default admin account:
> - username: admin@mail.com
> - password: password

#### Default user account:
> - username: user@mail.com
> - password: password
 
#### PhpMyAdmin running on: 
> http://localhost:8000


### Down app in development
```bash
make dev-down
```

### Down app in development with no cache
```bash
make dev-down-clean
```

## Production

### Running app in production
```bash
make deploy-up-clean
```

#### Web running on:
> http://localhost:8080

#### Default admin account:
> - username: admin@mail.com
> - password: password

#### Default user account:
> - username: user@mail.com
> - password: password

### Down app in production
```bash
make deploy-down
```

### Down app in production with no cache
```bash
make deploy-down-clean
```