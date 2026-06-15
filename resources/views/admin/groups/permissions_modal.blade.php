<div class="modal fade" id="permissionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="permissionsForm" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Assign Permissions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="permissions_list">
                    @foreach ($permissions ?? [] as $permission)
                        <div class="form-check">
                            <input type="checkbox" name="permission_ids[]" class="form-check-input"
                                value="{{ $permission->id }}">
                            <label class="form-check-label">
                                {{ $permission->name }} ({{ $permission->action }} - {{ $permission->subject }})
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>
