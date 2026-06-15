<div class="container mx-auto px-4 py-8">
  <div class="flex justify-between items-center mb-6">
    <h6 class="font-semibold mb-0 dark:text-white">
      {{ __("doc_activities.table.title") }}
    </h6>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-12">
    <div class="col-span-12">
      <div class="card !border-0">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table bordered-table mb-0">
              <thead>
                <tr>
                  <th scope="col">{{ __("doc_activities.table.time_column") }}</th>
                  <th scope="col">{{ __("doc_activities.table.event_column") }}</th>
                  <th scope="col">{{ __("doc_activities.table.user_column") }}</th>
                  <th scope="col">{{ __("doc_activities.table.changes_column") }}</th>
                </tr>
              </thead>
              <tbody>
                @if($doc_activities->count() > 0)
                  @foreach($doc_activities as $activity)
                    <tr>
                      <td>
                        {{ $activity->created_at->format('d M, Y \a\t h:i A') }}
                      </td>
                      <td>
                        {{ ucfirst($activity->event) }}
                      </td>
                      <td>
                        @if($activity->whodunnit)
                          @php
                            $user = $users_by_id[$activity->whodunnit] ?? null;
                          @endphp
                          {{ $user ? ucfirst($user->name) : "User ID: {$activity->whodunnit}" }}
                        @else
                          {{ __("doc_activities.table.no_user") }}
                        @endif
                      </td>
                      <td>
                        @php
                          $user = $activity->whodunnit ? ($users_by_id[$activity->whodunnit] ?? null) : null;
                        @endphp
                        {!! formatted_activity_changes($activity, $document, $user) !!}
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td colspan="4" class="text-center py-12 text-neutral-500 dark:text-neutral-400">
                      <div class="flex flex-col items-center justify-center">
                        <iconify-icon icon="heroicons-outline:document-magnifying-glass" class="text-4xl text-neutral-400 mb-3"></iconify-icon>
                        <p class="text-lg font-medium mb-1">
                          {{ __("doc_activities.table.no_activities_title") }}
                        </p>
                        <p class="text-sm">
                          {{ __("doc_activities.table.no_activities_description") }}
                        </p>
                      </div>
                    </td>
                  </tr>
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>