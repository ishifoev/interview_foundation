@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">GitHub Integration</div>

        <div class="card-body">
        <github-token-form></github-token-form>

        <fetch-starred-button></fetch-starred-button>
    </div>
</div>
@endsection