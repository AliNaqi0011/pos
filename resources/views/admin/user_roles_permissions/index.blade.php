@extends('layouts.main')

@section('content')
<div class="container">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h4 class="mb-0">Role Permissions Management</h4>
        </div>
        
        <div class="card-body">
            <!-- Role Permissions Section -->
            <h5 class="mb-3">Role Permissions</h5>
            <form id="rolePermissionsForm">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Permission</th>
                                @foreach($roles as $role)
                                <th class="text-center">{{ ucfirst($role->name) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $permission)
                            <tr>
                                <td>{{ ucwords(str_replace('_', ' ', $permission->name)) }}</td>
                                @foreach($roles as $role)
                                <td class="text-center align-middle">
                                    <div class="form-check d-inline-block">
                                        <input type="checkbox" class="form-check-input role-perm-checkbox" 
                                            data-role-id="{{ $role->id }}"
                                            data-perm-id="{{ $permission->id }}"
                                            id="role-{{ $role->id }}-perm-{{ $permission->id }}"
                                            @if($role->hasPermissionTo($permission->name)) checked @endif>
                                    </div>
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary">Save Role Permissions</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h4 class="mb-0">User Role Assignment</h4>
        </div>
        
        <div class="card-body">
            <!-- User Role Assignment Section -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="userSelect" class="form-label">Select User</label>
                    <select class="form-select" id="userSelect">
                        <option value="" selected disabled>-- Select User --</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <form id="userRolesForm">
                <h5 class="mb-3">Assign Roles to User</h5>
                <div class="row" id="userRolesContainer">
                    @foreach($roles as $role)
                    <div class="col-md-3 mb-2">
                        <div class="form-check">
                            <input class="form-check-input user-role-checkbox" type="checkbox" 
                                value="{{ $role->id }}" id="userRole{{ $role->id }}">
                            <label class="form-check-label" for="userRole{{ $role->id }}">
                                {{ ucfirst($role->name) }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary">Save User Roles</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Role Permissions Form
    $('#rolePermissionsForm').on('submit', function(e) {
        e.preventDefault();
        
        const rolePermissions = {};
        
        // Initialize all roles with empty arrays
        @foreach($roles as $role)
        rolePermissions[{{ $role->id }}] = [];
        @endforeach
        
        // Add checked permissions
        $('.role-perm-checkbox:checked').each(function() {
            const roleId = $(this).data('role-id');
            const permId = $(this).data('perm-id');
            rolePermissions[roleId].push(permId);
        });

        $.ajax({
            url: "{{ route('admin.user_roles_permissions.update_role_permissions') }}",
            method: 'POST',
            data: {
                role_permissions: rolePermissions,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                showAlert('Role permissions updated successfully!', 'success');
            },
            error: function(xhr) {
                showAlert('Failed to update role permissions', 'danger');
            }
        });
    });

    // User Role Assignment
    const $userSelect = $('#userSelect');
    const $userRolesForm = $('#userRolesForm');
    
    $userSelect.on('change', function() {
        const userId = $(this).val();
        if (!userId) return;

        $.get(`{{ url('admin/user-roles-permissions/permissions') }}/${userId}`, function(response) {
            $('.user-role-checkbox').prop('checked', false);
            response.roles.forEach(roleId => {
                $(`#userRole${roleId}`).prop('checked', true);
            });
        });> 
    });
    > $user->assignRole('sales');
    $userRolesForm.on('submit', function(e) {
        e.preventDefault();
        const userId = $userSelect.val();
        
        if (!userId) {
            showAlert('Please select a user first', 'warning');
            return;
        }

        const roleIds = [];
        $('.user-role-checkbox:checked').each(function() {
            roleIds.push($(this).val());
        });

        $.ajax({
            url: "{{ route('admin.user_roles_permissions.assign_roles') }}",
            method: 'POST',
            data: {
                user_id: userId,
                role_ids: roleIds,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                showAlert('User roles updated successfully!', 'success');
            },
            error: function(xhr) {
                showAlert('Failed to update user roles', 'danger');
            }
        });
    });

    function showAlert(message, type) {
        const $alert = $(`
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
        
        $('.container').prepend($alert);
        setTimeout(() => $alert.alert('close'), 5000);
    }
});
</script>

<style>
    .table th {
        white-space: nowrap;
        position: relative;
    }
    .form-check-input {
        transform: scale(1.25);
    }
    .card {
        margin-bottom: 20px;
    }
</style>
@endsection