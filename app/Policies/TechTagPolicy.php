<?php
namespace App\Policies;

use App\Models\TechTag;
use App\Models\User;

class TechTagPolicy
{
    public function viewAny(?User $user) { return true; }
    public function view(?User $user, TechTag $tag) { return true; }
    public function create(User $user) { return $user !== null; }
    public function update(User $user, TechTag $tag) { return $user !== null; }
    public function delete(User $user, TechTag $tag) { return $user !== null; }
}
