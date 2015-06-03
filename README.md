# caridea-auth
Caridea is a miniscule PHP web application library. This shrimpy fellow is what you'd use when you just want some helping hands and not a full-blown framework.

This is its authentication component. It provides a way to authenticate principals and store their identity. It will broadcast authentication events for any listeners.

Included are three adapters for authentication through MongoDB, PDO, and X.509 client SSL certificates. You can easily write your own adapter for other authentication sources like IMAP, LDAP, or OAuth2.

