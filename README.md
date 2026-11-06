## Roles

The application supports four roles:

- Owner: full access
- Administrator: inventory and user-management access
- Manager: inventory-management access
- Viewer: read-only access

Existing users become owners when the roles migration is applied.

To apply the migration:

```bash
/Applications/MAMP/Library/bin/mysql \
  -h 127.0.0.1 \
  -P 8889 \
  -u root \
  -p \
  inventory_saas_db \
  < database/migrations/001_add_roles_to_users.sql