@extends('admin.admin_dashboard')
@section('admin')
    <div class="page-content">

        <nav class="page-breadcrumb">
            <ol class="breadcrumb">
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Blog Comment All </h6>
                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        <th>Sl </th>
                                        <th>Post Title </th>
                                        <th>User Name </th>
                                        <th>Subject </th>
                                        {{-- <th>Message </th> --}}
                                        <th>Action </th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($comments as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item['post']['post_title'] }}</td>
                                            <td>{{ $item['user']['name'] }}</td>
                                            <td>{{ $item->subject }}</td>
                                            {{-- <td>{{ $item->message }}</td> --}}
                                            <td>
                                                @if ($item->parent_id == null)
                                                    {{-- Check if there are replies to this comment --}}
                                                    @php
                                                        $hasReplies = App\Models\Comment::where('parent_id',$item->id)->exists();
                                                    @endphp

                                                    @if ($hasReplies)
                                                        <a href="{{ route('admin.comment.reply', $item->id) }}"
                                                            class="btn btn-inverse-info">View Reply</a>
                                                    @else
                                                        <a href="{{ route('admin.comment.reply', $item->id) }}"
                                                            class="btn btn-inverse-warning">Reply Here</a>
                                                    @endif

                                                    <a href="{{ route('admin.delete.comment', $item->id) }}"
                                                        class="btn btn-inverse-danger" id="delete" title="Delete"> <i
                                                            data-feather="trash-2"></i> </a>
                                                @else
                                                    <a href="{{ route('admin.comment.reply', $item->parent_id) }}"
                                                        class="btn btn-inverse-info">View Comment</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
