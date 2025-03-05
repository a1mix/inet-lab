# Тестовое задание для компании "Лаборатория интернет"

## Запуск приложения

```
composer install
docker compose up
php bin/console doctrine:migrations:migrate
symfony server:start -d
```

##  REST API для User
### Примечание: header каждого запроса должен содержать ключ Authorization и значение Bearer token
### Создание пользователя
- **Метод:** POST
- **URL:** `/api/users/`
- **Тело запроса:**
```json
{
  "username": "string",
  "email": "string",
  "password": "string"
}
```
  
- **Ответ:**
```json
{
  "message": "Пользователь успешно создан",
  "userId": "int"
}
```


### Обновление информации пользователя
- **Метод:** PUT
- **URL:** `/api/users/{id}`
- **Тело запроса:**
```json
{
  "username": "string",
  "email": "string",
  "password": "string"
}
```

- **Ответ:**
```json
{
  "message": "Данные пользователя успешно обновлены",
  "userId": "int"
}
```


### Удаление пользователя
- **Метод:** DELETE
- **URL:** `/api/users/{id}`
- **Ответ:**
```json
{
  "message": "Пользователь успешно удален"
}
```
        


### Получение информации о пользователе
- **Метод:** GET
- **URL:** `/api/users/{id}`
- **Ответ:**
```json
{
  "id": "int",
  "username": "string",
  "email": "string"
}
```


## Аутентификация и Авторизация

Аутентификация реализована посредством JWT-токена из **LexikJWTAuthenticationBundle**.
Авторизация реализована с помощью access_control из **SecurityBundle**

### Получение JWT-токена
- **Метод:** POST
- **URL:** `/api/login_check`
- **Тело запроса:**
```json
{
  "email": "string",
  "password": "string"
}
```
- **Ответ:**

```json
{
  "token": "string"
}
```

## Регистрация
- **Метод:** POST
- **URL:** `/api/register`
- **Тело запроса:**
```json
{
  "email": "string",
  "password": "string",
  "username": "string"
}
```
- **Ответ:**

```json
{
  "userId": "int"
}
```