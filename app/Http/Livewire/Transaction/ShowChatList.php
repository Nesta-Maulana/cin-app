<?php

namespace App\Http\Livewire\Transaction;

use App\Repositories\Transaction\ChatRoom\ChatRoomRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use App\Models\ChatList as Model;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Repositories\Transaction\ChatList\ChatListRepositoryInterface;
use Log;

class ShowChatList extends Component
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
    public $lastFetchedAt;

    protected $repository;
    public function mount(ChatRoomRepository $repository)
    {
        $this->repository = $repository;
    }
    public function hydrate()
    {
        $this->repository = app(ChatRoomRepository::class);
    }

    public function render()
    {
        try {
            $orderBy = ['updated_at' => 'desc'];
            $with = [
                'contact',
                'chatDetail'
            ];
            // Apply search filter
            $scope['hasChatDetail'] = [];
            $scope['filterByBot'] = 1;
            $chat = $this->repository->getData($scope, $with, $orderBy, null);
            $date = Carbon::parse($chat[0]->updated_at);

            // Convert to a specific timezone (e.g., Asia/Jakarta)
            $date->setTimezone('Asia/Jakarta');

            // Format the date to the desired format
            $this->lastFetchedAt = $date->format('Y-m-d H:i:s');



            return view('livewire.transaction.show-chat-list', [
                'chats' => $chat,
                'last_updated' => $this->lastFetchedAt
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Error fetching permissions: ' . $e->getMessage());
            return view('livewire.transaction.show-chat-list', [
                'chats' => collect([]),
                'last_updated' => ''
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

    public function updated($propertyName)
    {
        $this->render();
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
    public function latestUpdated()
    {

        /*  $orderBy = ['updated_at' => 'desc'];
         $with = [];
         // Apply search filter
         $scope['hasChatDetail'] = [];
         $scope['filterByBot'] = 1;
         $chat = $this->repository->getData($scope, $with, $orderBy, null, [], 'last');
         Log::info($chat->updated_at);
         Log::info($this->lastFetchedAt);
         if (strtotime($this->lastFetchedAt) !== strtotime($chat->updated_at)) {
             $this->render();
         } */
    }

    public function syncChat()
    {
        $this->render();
    }
}


