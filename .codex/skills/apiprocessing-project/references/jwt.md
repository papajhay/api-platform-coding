# JWT

This project uses Lexik JWT authentication plus a refresh token bundle.

## Config files

- `config/packages/lexik_jwt_authentication.yaml`
- `config/packages/gesdinet_jwt_refresh_token.yaml`
- `.env`
- `config/jwt/private.pem`
- `config/jwt/public.pem`

## Lexik JWT configuration

```yaml
lexik_jwt_authentication:
    secret_key: '%env(resolve:JWT_SECRET_KEY)%'
    public_key: '%env(resolve:JWT_PUBLIC_KEY)%'
    pass_phrase: '%env(JWT_PASSPHRASE)%'
    token_ttl: '%env(int:JWT_TOKEN_TTL)%'
```

## Environment variables

```dotenv
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=...
JWT_TOKEN_TTL=172800
JWT_REFRESH_TOKEN_TTL=2592000
```

## Refresh tokens

```yaml
gesdinet_jwt_refresh_token:
    refresh_token_class: App\Entity\RefreshToken
    ttl: '%env(int:JWT_REFRESH_TOKEN_TTL)%'
    ttl_update: true
    token_parameter_name: refresh_token
```

## Bootstrap command

```bash
php bin/console lexik:jwt:generate-keypair
```

## Login flow

The login endpoint is `/api/login_check` and the API platform uses JWT in the `Authorization` header.

The project also exposes refresh token usage through `api_refresh_token`.

## Existing conventions

- JWT is stateless.
- The API expects `email` and `password` for login.
- Protected API requests use `Authorization: Bearer <token>`.
- Refresh tokens are backed by `App\Entity\RefreshToken`.
