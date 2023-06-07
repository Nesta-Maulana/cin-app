<div>
    <script>
        document.addEventListener('livewire:load', function () {
            $.fn.extend({
                treed: function (o) {
                
                var openedClass = 'fa-solid fa-folder-open fs-3';
                var closedClass = 'fa-solid fa-folder fs-3';
                
                if (typeof o != 'undefined'){
                    if (typeof o.openedClass != 'undefined'){
                    openedClass = o.openedClass;
                    }
                    if (typeof o.closedClass != 'undefined'){
                    closedClass = o.closedClass;
                    }
                };
                
                    //initialize each of the top levels
                    var tree = $(this);
                    tree.addClass("tree");
                    tree.find('li').has("ul").each(function () {
                        var branch = $(this); //li with children ul
                        branch.prepend("<i class='indicator glyphicon " + closedClass + "'></i>");
                        branch.addClass('branch');
                        branch.on('click', function (e) {
                            if (this == e.target) {
                                var icon = $(this).children('i:first');
                                icon.toggleClass(openedClass + " " + closedClass);
                                $(this).children().children().toggle();
                            }
                        })
                        branch.children().children().toggle();
                    });
                    
                    //fire event from the dynamically added icon
                    tree.find('.branch .indicator').each(function(){
                        $(this).on('click', function () {
                            $(this).closest('li').click();
                        });
                    });
                    //fire event to open branch if the li contains an anchor instead of text
                    tree.find('.branch>a').each(function () {
                        $(this).on('click', function (e) {
                            $(this).closest('li').click();
                            e.preventDefault();
                        });
                    });
                    //fire event to open branch if the li contains a button instead of text
                    tree.find('.branch>button').each(function () {
                        $(this).on('click', function (e) {
                            $(this).closest('li').click();
                            e.preventDefault();
                        });
                    });
                }
            });
        
            //Initialization of treeviews
            $('#tree1').treed();
            $('#tree2').treed({openedClass:'glyphicon-folder-open', closedClass:'glyphicon-folder-close'});
            $('#tree3').treed({openedClass:'glyphicon-chevron-right', closedClass:'glyphicon-chevron-down'});
        
            window.livewire.on('dragdrop',()=>{
                $('#tree1').treed();
            });
        })
    </script>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header border-bottom d-md-flex justify-content-md-between align-items-md-center">
                    <div class="my-1 text-center text-md-start">
                        <label>
                            <input wire:model.debounce.500ms="search" type="search" class="form-control"
                                placeholder="Search..">
                        </label>
                    </div>
                    <div
                        class="text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column">
                        @if (!$selected)
                        <div class="my-1 me-md-2">
                            <label>
                                <select wire:model="paginate" class="form-select">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </label>
                        </div>
                        @can('create-menu')
                        <div class="flex-wrap my-1">
                            <a href="{{ route('menu.create') }}"
                                class="btn btn-secondary text-white add-new btn-primary">
                                <span>
                                    <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
                                    <span>
                                        {{ $title }}
                                    </span>
                                </span>
                            </a>
                        </div>
                        @endcan
                        @else
                        <div class="my-1 me-md-2">
                            <span class="px-1">
                                {{ count($selected) }} data terpilih
                            </span>
                        </div>
                        <div class="flex-wrap my-1">
                            <a href="#" class="btn btn-secondary add-new btn-label-primary" data-bs-toggle="modal"
                                data-bs-target="#modalSelectedStatus">
                                Update Status
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table border-top">
                        <thead>
                            <tr>
                                <th style="width: 2%"></th>
                                <th>Menu</th>
                                <th>Permission</th>
                                <th>Main Menu</th>
                                <th class="text-center">No. Urut</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($table as $key => $item)
                            @php
                            $updateRoute = route('menu.update', $item->id);
                            @endphp

                            <tr wire:key="main-{{ $item->id }}">
                                <td style="width: 2%">
                                    <i class="ti ti-{{ $item->icon }} ti-sm mx-2 fs-5"></i>
                                </td>

                                <td class="col-3">
                                    <div class="d-flex flex-column">
                                        <a href="#" class="text-body text-truncate">
                                            <span class="fw-semibold">
                                                {{ $item->name }}
                                            </span>
                                        </a>
                                        <small class="text-muted">/{{ $item->url }}</small>
                                    </div>
                                </td>

                                <td class="col-3">
                                    @if($item->permission_id)
                                    {{ $item->permission->name }}
                                    @endif
                                </td>

                                <td class="col-3">
                                    @if($item->main_menu)
                                    {{ $item->parent->name }}
                                    @endif
                                </td>

                                <td class="col-3 text-center">
                                    {{ $item->sort }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        @can('update-menu')
                                        <a href="{{ route('menu.edit', $item->id) }}" class="action-btn" title="edit">
                                            <i class="ti ti-edit ti-sm me-2 fs-5"></i>
                                        </a>
                                        @endcan

                                        @can('delete-menu')
                                        <a href="javascript:;" class="action-btn" title="delete" data-bs-toggle="modal"
                                            data-bs-target="#modalDelete{{ $item->id }}">
                                            <i class="ti ti-trash ti-sm mx-2 fs-5"></i>
                                        </a>
                                        @include('admin.modal.delete')
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <div class="text-center col-md-7 mx-auto px-3 pt-3">
                                <div class="alert alert-secondary">
                                    Data tidak ditemukan
                                </div>
                            </div>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-body d-md-flex justify-content-md-between align-items-center pt-3 pb-2">
                    <div class="align-self-start my-2 d-none d-md-block text-muted">
                        <small>
                            Showing {{ $table->firstItem() }} to {{ $table->lastItem() }} of {{ $table->total() }} data
                        </small>
                    </div>
                    {{ $table->links() }}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title m-0 me-2">Urutan Menu</h5>
                    <a href="{{ route('menu.index') }}" class="btn btn-sm btn-label-primary" id="teamMemberList">
                        <i class="ti ti-refresh ti-xs me-1"></i>
                        Sync
                    </a>
                </div>
                <div class="card-body">
                    <div class="fw-semibold">
                        <div id="tree1">
                            <div wire:sortable="updateParentOrder" wire:sortable-group="updateChildGroup">
                                @foreach (getMenu() as $menu)
                                <div class="ms-1 d-flex align-items-center text-body h6 mt-2"
                                    wire:key="menu-{{ $menu->id }}" wire:sortable.item="{{ $menu->id }}">
                                    <i class="ti ti-{{ $menu->icon }} text-body me-2"></i>
                                    {{ $menu->name }}
                                </div>

                                <ul wire:sortable-group.item-group="{{ $menu->id }}">
                                    @foreach ($menu->subMenu as $child)
                                    <li wire:key="child-{{ $child->id }}" wire:sortable-group.item="{{ $child->id }}">
                                        <div class="ms-1">
                                            {{ $child->name }}
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>