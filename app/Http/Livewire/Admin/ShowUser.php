<?php

namespace App\Http\Livewire\Admin;

use App\Repositories\Master\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Exception;

class ShowUser extends Component
{
    use WithPagination, LivewireAlert;

    protected $paginationTheme = 'bootstrap';
    protected $paginationClasses = 'd-flex align-items-center';

    public $paginate = 10;
    public $search = "";
    public $selected = [];
    public $selectAll = false;

    public $title, $modelId, $password = 'password', $status = false;
    protected $userRepository;

    public function mount(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function hydrate()
    {
        $this->userRepository = app(UserRepositoryInterface::class);
    }

    public function render()
    {
        try {
            $scope = [];
            $with = ['roles'];
            $orderBy = ['id' => 'ASC'];
            $conditions = [];
            switch (Auth::user()->getRoleNames()->first()) {
                case 'Tech Lead':
                    $scope = ['techLead' => []];
                    break;

                default:
                    $scope = ['administrator' => []];
                    break;
            }

            // Apply search filter
            if (!empty($this->search)) {
                $scope['filter'] = [$this->search];
            }
            $table = $this->userRepository->getData($scope, $with, $orderBy, $this->paginate);
            return view('livewire.admin.show-user', [
                'table' => $table,
            ]);
        } catch (Exception $e) {
            return view('livewire.admin.show-user', [
                'table' => collect([]), // Return empty collection in case of error
            ])->withErrors(['message' => 'Error fetching users: ' . $e->getMessage()]);
        }
    }

    // Misc
    public function resetCreateForm()
    {
        $data = ['modelId', 'status'];
        foreach ($data as $item) {
            $this->$item = "";
        }
        $this->password = 'password';
        $this->status = false;
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

    // update
    public function updatePassword($id)
    {
        try {
            $data['password'] = $this->password;
            $data['updated_by'] = auth()->user()->id;
            $user = $this->userRepository->update($id, $data);
            return $this->alert('success', 'Password updated successfully!', [
                'showCloseButton' => true,
            ]);
        } catch (Exception $e) {
            return $this->alert('error', 'Error updating password: ' . $e->getMessage(), [
                'showCloseButton' => true,
            ]);
        }
    }

    public function updateStatus($id, $status)
    {
        try {
            $data['status'] = $status;
            $data['updated_by'] = Auth::user()->id;
            $user = $this->userRepository->update($id, $data);
            return $this->alert('success', 'Status updated successfully!', [
                'showCloseButton' => true,
            ]);
        } catch (Exception $e) {
            return $this->alert('error', 'Error updating status: ' . $e->getMessage(), [
                'showCloseButton' => true,
            ]);
        }
    }
}
