<?php

namespace App\Http\Livewire\Transaction;

use App\Repositories\Transaction\ChatRoomDetail\ChatRoomDetailRepositoryInterface;
use Livewire\Component;
use App\Models\ChatRoom as Model;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Repositories\Transaction\ChatRoom\ChatRoomRepositoryInterface;

class ShowChatRoom extends Component
{
    use LivewireAlert;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $paginationClasses = 'd-flex align-items-center';

    public $paginate = 10;
    public $search = "";
    public $selected = [];
    public $selectAll = false;

    public $title, $model, $modelId;

    protected $repository;

    public function mount(ChatRoomDetailRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    public function hydrate()
    {
        $this->repository = app(ChatRoomDetailRepositoryInterface::class);
    }

    public function render()
    {
        try {
            $orderBy = ['message_time' => 'asc'];
            $with = [
                'chatRoom' => [
                    'contact'
                ]
            ];
            // Apply search filter
            $scope = [];
            $chat = $this->repository->getData($scope, $with, $orderBy, null, [
                ['chat_room_id', '=', '43']
            ]);
            return view('livewire.transaction.show-chat-room', [
                'chat' => $chat,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Error fetching permissions: ' . $e->getMessage());
            return view('livewire.transaction.show-chat-room', [
                'chat' => collect([]),
            ]);
        }
    }

    // Misc
    public function resetCreateForm()
    {
        $data = ['modelId',];
        foreach ($data as $item) {
            $this->$item = "";
        }
    }

    public function closeModal()
    {
        $this->dispatchBrowserEvent('close-modal');
        $this->resetErrorBag();
        $this->resetCreateForm();
    }

    public function updated()
    {
        $this->resetPage();
    }

    public function modelId($id)
    {
        $this->repositoryId = $id;
    }

    public function updatedSelectAll($value)
    {
        $model = $this->repository
            // ->filter($this->search)
            ->getData();

        if ($value) {
            $this->selected = $model->pluck('id');
        } else {
            $this->selected = [];
        }
    }
    public function syncRoomChat()
    {
        $this->render();
        $this->dispatchBrowserEvent('sync-room-chat-complete');
    }
}


