<?php

declare(strict_types=1);

namespace Tests\SamMcDonald\Norm\Unit\Entities;

use SamMcDonald\Norm\Attributes\Orm\NormColumnMapping;
use SamMcDonald\Norm\Attributes\Orm\NormEntity;
use SamMcDonald\Norm\Attributes\Orm\NormEntityTable;
use SamMcDonald\Norm\Attributes\Orm\NormPrimaryKey;

#[NormEntity]
#[NormEntityTable('Users')]
class User
{
    #[NormPrimaryKey]
    #[NormColumnMapping('id')]
    public int $id;

    #[NormColumnMapping('username')]
    public string $userFirstName;

    #[NormColumnMapping('email')]
    public string|null $email = null;
}
