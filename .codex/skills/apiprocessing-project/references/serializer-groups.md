# Serializer Groups

The project keeps serializer metadata in YAML files under `config/serializer/`.

## Files

- `config/serializer/Post.yaml`
- `config/serializer/User.yaml`
- `config/serializer/Comment.yaml`

## Post groups

```yaml
App\Entity\Post:
    attributes:
        id:
            groups: ['post:read']
        title:
            groups: ['post:read', 'post:write']
        content:
            groups: ['post:read', 'post:write']
        imageName:
            groups: ['post:read']
        imageUrl:
            groups: ['post:read']
        author:
            groups: ['post:read', 'post:write']
        comments:
            groups: ['post:read']
```

## User groups

```yaml
App\Entity\User:
    attributes:
        id:
            groups: ['user:read']
        email:
            groups: ['user:read', 'user:write']
        roles:
            groups: ['user:read', 'user:write']
        password:
            groups: ['user:write']
```

## Comment groups

```yaml
App\Entity\Comment:
    attributes:
        id:
            groups: ['comment:read']
        content:
            groups: ['comment:read', 'comment:write']
        post:
            groups: ['comment:read', 'comment:write']
        author:
            groups: ['comment:read', 'comment:write']
```

## Matching API Platform contexts

The YAML resources use the same read/write naming convention:

- `post:read` / `post:write`
- `user:read` / `user:write`
- `comment:read` / `comment:write`

## Existing conventions

- Read groups include display fields and relations.
- Write groups include mutable scalar fields and relations.
- Password is write-only.
- Upload metadata is not exposed as a write target unless the endpoint explicitly handles multipart upload.
