@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-5xl mx-auto">

    <h1 class="text-xl font-semibold mb-6">Newsletter History</h1>

    <div class="bg-white border rounded-xl overflow-hidden">

        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3">Subject</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach($campaigns as $c)
                <tr class="border-t">
                    <td class="p-3">{{ $c->subject }}</td>
                    <td>{{ $c->created_at->format('d M Y') }}</td>
                    <td>{{ $c->total_sent }}</td>
                </tr>
                @endforeach
            </tbody>

        </table>

    </div>

</div>
@endsection
