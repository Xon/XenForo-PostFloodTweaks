XenForo-PostFloodTweaks
======================

Provides user group permissions for the following post rate limiting options:

- Post Reply - Enable Per thread rate limiting
- Post Reply - Per thread rate limiting
- Post Reply - Enable Per node rate limiting
- Post Reply - Per node post rate limiting
- Post Reply - General rate limiting

This permits the posting rate to be managed per node, and per user group.

The per thread/node option allows decoupling of the global flood limiter from posting in different sections.

Matching order:
- Per thread rate limiting.
- Per node rate limiting.
- General post rate limiting.
- XF Global post rate limiting (reports/posts/profile posts/etc).

No extra queries required.