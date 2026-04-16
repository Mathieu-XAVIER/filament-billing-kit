<?php

namespace Mxavier\FilamentBillingKit\Livewire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Mxavier\FilamentBillingKit\Models\BillingInvoice;

#[Layout('filament-billing-kit::layouts.billing')]
class MyInvoicesPage extends Component
{
    use WithPagination;

    public function getBillable(): ?Model
    {
        $mode = config('filament-billing-kit.mode', 'mono-tenant');

        return $mode === 'multi-tenant'
            ? filament()->getTenant()
            : auth()->user();
    }

    public function getInvoices(): LengthAwarePaginator
    {
        $billable = $this->getBillable();

        return BillingInvoice::query()
            ->when($billable, fn ($q) => $q
                ->where('billable_type', get_class($billable))
                ->where('billable_id', $billable->getKey())
            )
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    public function render(): View
    {
        return view('filament-billing-kit::livewire.my-invoices-page', [
            'invoices' => $this->getInvoices(),
        ]);
    }
}
