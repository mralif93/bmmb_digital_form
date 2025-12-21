<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

/**
 * Unit tests for User role helper methods.
 * 
 * Tests the role-based access control logic for the eForm submission workflow.
 */
class UserRoleTest extends TestCase
{
    /**
     * Test isCFE() returns true for CFE role.
     */
    public function test_is_cfe_returns_true_for_cfe_role(): void
    {
        $user = new User();
        $user->role = 'cfe';

        $this->assertTrue($user->isCFE());
    }

    /**
     * Test isCFE() returns true for MAP position 3.
     */
    public function test_is_cfe_returns_true_for_map_position_3(): void
    {
        $user = new User();
        $user->map_position = '3';

        $this->assertTrue($user->isCFE());
    }

    /**
     * Test isCFE() returns false for other roles.
     */
    public function test_is_cfe_returns_false_for_other_roles(): void
    {
        $roles = ['admin', 'branch_manager', 'headquarters', 'iam', 'operation_officer'];

        foreach ($roles as $role) {
            $user = new User();
            $user->role = $role;
            $this->assertFalse($user->isCFE(), "isCFE() should return false for role: {$role}");
        }
    }

    /**
     * Test isBM() returns true for branch_manager role.
     */
    public function test_is_bm_returns_true_for_branch_manager_role(): void
    {
        $user = new User();
        $user->role = 'branch_manager';

        $this->assertTrue($user->isBM());
    }

    /**
     * Test isBM() returns false for other roles.
     */
    public function test_is_bm_returns_false_for_other_roles(): void
    {
        $roles = ['admin', 'cfe', 'headquarters', 'iam', 'operation_officer'];

        foreach ($roles as $role) {
            $user = new User();
            $user->role = $role;
            $this->assertFalse($user->isBM(), "isBM() should return false for role: {$role}");
        }
    }

    /**
     * Test isHQ() returns true for headquarters role.
     */
    public function test_is_hq_returns_true_for_headquarters_role(): void
    {
        $user = new User();
        $user->role = 'headquarters';

        $this->assertTrue($user->isHQ());
    }

    /**
     * Test isHQ() returns false for other roles.
     */
    public function test_is_hq_returns_false_for_other_roles(): void
    {
        $roles = ['admin', 'cfe', 'branch_manager', 'iam', 'operation_officer'];

        foreach ($roles as $role) {
            $user = new User();
            $user->role = $role;
            $this->assertFalse($user->isHQ(), "isHQ() should return false for role: {$role}");
        }
    }

    /**
     * Test hasAdminAccess() returns true for admin role.
     */
    public function test_has_admin_access_returns_true_for_admin(): void
    {
        $user = new User();
        $user->role = 'admin';

        $this->assertTrue($user->hasAdminAccess());
    }

    /**
     * Test hasAdminAccess() returns true for HQ role.
     */
    public function test_has_admin_access_returns_true_for_hq(): void
    {
        $user = new User();
        $user->role = 'headquarters';

        $this->assertTrue($user->hasAdminAccess());
    }

    /**
     * Test hasAdminAccess() returns true for BM role.
     */
    public function test_has_admin_access_returns_true_for_bm(): void
    {
        $user = new User();
        $user->role = 'branch_manager';

        $this->assertTrue($user->hasAdminAccess());
    }

    /**
     * Test hasAdminAccess() returns true for CFE role.
     */
    public function test_has_admin_access_returns_true_for_cfe(): void
    {
        $user = new User();
        $user->role = 'cfe';

        $this->assertTrue($user->hasAdminAccess());
    }

    /**
     * Test getRoleDisplayAttribute() returns correct display names.
     */
    public function test_get_role_display_returns_correct_names(): void
    {
        $expectedDisplayNames = [
            'admin' => 'Administrator',
            'branch_manager' => 'Branch Manager',
            'assistant_branch_manager' => 'Assistant Branch Manager',
            'operation_officer' => 'Operations Officer',
            'headquarters' => 'Headquarters',
            'iam' => 'Identity & Access Management',
            'cfe' => 'Customer Finance Executive',
        ];

        foreach ($expectedDisplayNames as $role => $expectedDisplay) {
            $user = new User();
            $user->role = $role;
            $this->assertEquals(
                $expectedDisplay,
                $user->role_display,
                "Role display for '{$role}' should be '{$expectedDisplay}'"
            );
        }
    }

    /**
     * Test workflow role permissions - CFE can take up.
     */
    public function test_cfe_can_take_up_submission(): void
    {
        $user = new User();
        $user->role = 'cfe';

        // CFE should be able to take up (isCFE or isBM)
        $canTakeUp = $user->isCFE() || $user->isBM();

        $this->assertTrue($canTakeUp);
    }

    /**
     * Test workflow role permissions - BM can take up.
     */
    public function test_bm_can_take_up_submission(): void
    {
        $user = new User();
        $user->role = 'branch_manager';

        // BM should be able to take up (isCFE or isBM)
        $canTakeUp = $user->isCFE() || $user->isBM();

        $this->assertTrue($canTakeUp);
    }

    /**
     * Test workflow role permissions - HQ cannot take up.
     */
    public function test_hq_cannot_take_up_submission(): void
    {
        $user = new User();
        $user->role = 'headquarters';

        // HQ should NOT be able to take up
        $canTakeUp = $user->isCFE() || $user->isBM();

        $this->assertFalse($canTakeUp);
    }

    /**
     * Test workflow role permissions - Admin cannot take up.
     */
    public function test_admin_cannot_take_up_submission(): void
    {
        $user = new User();
        $user->role = 'admin';

        // Admin should NOT be able to take up (workflow is for CFE/BM)
        $canTakeUp = $user->isCFE() || $user->isBM();

        $this->assertFalse($canTakeUp);
    }

    /**
     * Test MAP position 3 triggers CFE role check.
     */
    public function test_map_position_3_triggers_cfe(): void
    {
        $user = new User();
        $user->map_position = '3';

        // CFE is special - it checks map_position
        $this->assertTrue($user->isCFE());
    }

    /**
     * Test workflow permissions with MAP position 3 (CFE).
     */
    public function test_map_position_3_can_take_up(): void
    {
        $user = new User();
        $user->map_position = '3';

        // MAP position 3 = CFE, should be able to take up
        $canTakeUp = $user->isCFE() || $user->isBM();

        $this->assertTrue($canTakeUp);
    }
}
