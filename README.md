# Wordpress Article Feedback

A Wordpress plugin that create custom endpoints to compute if a post is helpful or not.


## API Reference

#### Like a post

```http
  POST /wp-json/wp-post-feedback/v1/post/{id}/like
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `id` | `integer` | **Required**. The post ID |

#### Unlike a post

```http
  POST /wp-json/wp-post-feedback/v1/post/{id}/unlike
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `id`      | `integer` | **Required**. The post ID |

The both endpoints need you to be authenticated and with `edit_others_posts` capability.

The number of likes and unlikes can be view on post list table.
## Suggestions

Plugins:
- [JWT Authentication for WP REST API](https://wordpress.org/plugins/jwt-authentication-for-wp-rest-api/)