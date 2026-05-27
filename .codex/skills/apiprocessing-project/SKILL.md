---
name: apiprocessing-project
description: Generate API Platform YAML for this Symfony project by matching the existing YAML-only resource definitions, serializer groups, operation security, filters, multipart upload handling, and the small set of custom controllers already used in src/Controller/. Use this skill when working under config/api_platform/resources/, config/serializer/, config/packages/api_platform.yaml, and API-related controllers so new resources stay compatible with the current architecture.
metadata:
  short-description: Symfony API Platform YAML patterns for this project
---

# API Processing Project

Use this skill when bootstrapping or updating Symfony 7 + API Platform + JWT code in this repository.

## Workflow

1. Load the reference files under `references/`.
2. Extract the current conventions before generating anything.
3. Keep the output YAML-first.
4. Do not introduce DTOs, State Providers, State Processors, or PHP filter classes.

## References

Load the minimum reference that matches the task:

- [bootstrap-symfony.md](references/bootstrap-symfony.md) for initial project setup and commands.
- [jwt.md](references/jwt.md) for Lexik JWT, refresh tokens, and `/api/login_check`.
- [security.md](references/security.md) for firewalls, roles, and access control.
- [api-resources.md](references/api-resources.md) for API Platform resource YAML patterns.
- [serializer-groups.md](references/serializer-groups.md) for serializer metadata and group naming.
- [uploads.md](references/uploads.md) for multipart uploads and the custom API controller pattern.

For project-wide bootstrapping, read all files in `references/`.

## Project rules

- The project primarily uses YAML API Platform resources in `config/api_platform/resources/`.
- The project uses YAML serializer metadata in `config/serializer/`.
- API Platform routing comes from `config/routes/api_platform.yaml`.
- API access is role-based and must match the existing expressions.
- Multipart uploads use `read: false`, `deserialize: false`, and a custom controller only where the project already does this.

## Prohibited anti-patterns

Do not generate:

- DTOs
- State Providers
- State Processors
- PHP filter classes
- custom resource attributes when the repository already defines the resource in YAML
- new architectural layers that are not already in the project

## Command log

These are the analysis commands used to extract the project conventions:

```bash
sed -n '1,260p' composer.json
sed -n '1,220p' .env
sed -n '1,220p' .env.local
sed -n '1,220p' config/packages/api_platform.yaml
sed -n '1,220p' config/packages/lexik_jwt_authentication.yaml
sed -n '1,220p' config/packages/gesdinet_jwt_refresh_token.yaml
sed -n '1,220p' config/packages/vich_uploader.yaml
sed -n '1,220p' config/packages/nelmio_cors.yaml
sed -n '1,220p' config/packages/security.yaml
sed -n '1,220p' config/routes/api_platform.yaml
find config/api_platform/resources -type f
find config/serializer -type f
find src/Entity -maxdepth 1 -type f
find src/Controller -type f
rg -n "read:\\s*false|deserialize:\\s*false|multipart|filters:|operations:|security:" src config -g '!vendor'
```

Use the reference files rather than repeating the analysis every time unless the project changes.
