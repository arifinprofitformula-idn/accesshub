<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserPasswordHashingTest extends TestCase
{
    public function test_plain_password_assignment_is_stored_as_bcrypt(): void
    {
        $user = new User();
        $user->password = 'secret-password';

        $this->assertTrue(Hash::check('secret-password', $user->password));
        $this->assertSame('bcrypt', password_get_info($user->password)['algoName']);
    }

    public function test_existing_bcrypt_hash_is_not_rehashed_again(): void
    {
        $bcryptHash = Hash::driver('bcrypt')->make('secret-password');

        $user = new User();
        $user->password = $bcryptHash;

        $this->assertSame($bcryptHash, $user->password);
        $this->assertSame('bcrypt', password_get_info($user->password)['algoName']);
    }
}
