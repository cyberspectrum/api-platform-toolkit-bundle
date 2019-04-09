# api-platform toolkit

## Installation

```
composer require cyberspectrum/api-platform-toolkit-bundle
```

## Configuration

```
api_platform_toolkit:
  # Enable custom expression language providers
  enable_expression_language: true
  # Enable JWT handling - this can be disabled.
  lexik_jwt:
    # Enable documentation handling (adds the login endpoint to swagger docs).
    add_documentation: true
    # The default ttl if not specified in request (defaults to 3600)
    default_ttl: 3600
    # The login url.
    login_url: '/api/login_check'
```

## Features:

### Add own providers to expression language.

This bundle supports to add tagged services to the api platform expression 
language.

To add your own `ExpressionFunctionProviderInterface` implementor use this 
service registration:
```
App\ExpressionLanguage\SomeExpressionLanguageProvider:
    tags:
        - { name: csap_toolkit.security.expression_language }
```



## TODO

- Make PR for expression language support in `api-platform/core`.
- Make JWT TTL support configuration optional.
- Add min and max value support for JWT TTL.
