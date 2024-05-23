<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Invoice;
use App\Http\Requests\BulkStoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\InvoiceResource;
use App\Http\Resources\V1\InvoiceCollection;
use Illuminate\Http\Request;
use App\Filters\V1\InvoicesFilter;
use Illuminate\Support\Arr;


class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new InvoicesFilter();
        $queryItems = $filter->transform($request);

        if (count($queryItems) == 0) {
            return new InvoiceCollection(Invoice::paginate());
        } else {
            // Apply the query constraints to the Eloquent query
            $query = Invoice::query();
            foreach ($queryItems as $condition) {
                if ($condition[1] === 'isNull') {
                    $query->whereNull($condition[0]);
                } elseif ($condition[1] === 'isNotNull') {
                    $query->whereNotNull($condition[0]);
                } else {
                    $query->where($condition[0], $condition[1], $condition[2]);
                }
            }

            // Paginate the filtered results and append query parameters to pagination links
            $invoices = $query->paginate()->appends($request->query());

            return new InvoiceCollection($invoices);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BulkStoreInvoiceRequest $request)
    {
        //
    }
    public function bulkStore(Request $request)
    {
        $bulk = collect($request->all())->map(function ($arr, $key) {
            return Arr::except($arr, ['customerId', 'billedDate', 'paidDate']);
        });

        Invoice::insert($bulk->toArray());
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        return new InvoiceResource($invoice);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
