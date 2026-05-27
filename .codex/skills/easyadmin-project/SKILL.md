---
name: easyadmin-project
description: Generate and modify EasyAdmin code for this Symfony project by matching the existing admin controllers, route wiring, field choices, action patterns, and security model. Use when working under src/Controller/Admin/, config/packages/security.yaml, and config/routes/easyadmin.yaml so new admin code stays compatible with the project's conventions.
metadata:
  short-description: Symfony EasyAdmin patterns for this project
---

# EasyAdmin Project

Use this skill when creating or updating EasyAdmin code in this repository.

## How to use

Load the reference files under `references/` before generating or changing admin code. They contain the actual project conventions and the code examples extracted from this repository.

## References

- [security.md](references/security.md)
- [dashboard.md](references/dashboard.md)
- [crud.md](references/crud.md)
- [fields.md](references/fields.md)
- [entities.md](references/entities.md)

For any EasyAdmin task, load all files in `references/` to preserve the current conventions.

## Guardrails

- Do not invent new admin roles.
- Do not introduce new EasyAdmin conventions that do not already exist here.
- Keep new admin code aligned with the current controller structure, field classes, action set, and CRUD settings.
- Keep using `config/routes/easyadmin.yaml`; this project does not use `config/packages/easy_admin.yaml`.
