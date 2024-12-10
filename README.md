# Norm (A Simple ORM Library for Educational Use)
[![Source](https://img.shields.io/badge/source-S_McDonald-blue.svg)](https://github.com/s-mcdonald/Norm)
[![License](https://img.shields.io/badge/license-MIT-gold.svg)](https://github.com/s-mcdonald/Norm)

`Norm` is a straightforward Object-Relational Mapping (ORM) library designed for educational purposes. It is not intended for production use.

## Usage

### Mapping Fields

To map fields in your entity classes, use attributes to define the database table and columns:

```php
#[NormEntityTable('users')]
class User
{
    #[NormPrimaryKey()]
    #[NormColumnMapping('user_id')]
    public int $id;

    #[NormColumnMapping('first_name')]
    public string $name;

    public array $somethingElseNotMapped = [];
}
```

### Creating a Connection and EntityManager

Establish a connection and initialize the `EntityManager`:

```php
use SamMcDonald\Norm;

$entityManager = new Norm\EntityManager(
    Norm\Factory\ConnectionFactory::createConnection()
);
```

### Creating an EntityRepository

Obtain a repository for your entities to perform CRUD operations:

```php
use SamMcDonald\Norm;

$userRepo = $entityManager->getRepository(User::class);
$user = $userRepo->find(123);
```

### Persisting and Deleting Entities

You can persist new entities or delete existing ones using the `EntityManager`:

```php
$entityManager->persist($user);

$userRepo = $entityManager->getRepository(User::class);
$userRepo->delete($user->id);
```

## License

Norm is licensed under the terms of the [MIT License](http://opensource.org/licenses/MIT). For more details, see the LICENSE file.
