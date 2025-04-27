@extends('agent.agent_dashboard')
@section('agent')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

    <div class="page-content">

        <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Property Details </h6>
                        <div class="table-responsive">
                            <table class="table table-striped">

                                <tbody>
                                    <tr>
                                        <td>Property Name </td>
                                        <td><code>{{ $property->property_name }}</code></td>
                                    </tr>

                                    <tr>
                                        <td>Property Status </td>
                                        <td><code>{{ $property->property_status }}</code></td>
                                    </tr>

                                    <tr>
                                        <td>Lowest Price </td>
                                        <td><code>{{ $property->lowest_price }}</code></td>
                                    </tr>

                                    <tr>
                                        <td>Max Price </td>
                                        <td><code>{{ $property->max_price }}</code></td>
                                    </tr>
                                    <tr>
                                        <td>BedRooms </td>
                                        <td><code>{{ $property->bedrooms }}</code></td>
                                    </tr>

                                    <tr>
                                        <td>Bathrooms </td>
                                        <td><code>{{ $property->bathrooms }}</code></td>
                                    </tr>
                                    <tr>
                                        <td>Garage </td>
                                        <td><code>{{ $property->garage }}</code></td>
                                    </tr>
                                    <tr>
                                        <td>Garage Size </td>
                                        <td><code>{{ $property->garage_size }}</code></td>
                                    </tr>
                                    <tr>
                                        <td>Address </td>
                                        <td><code>{{ $property->address }}</code></td>
                                    </tr>
                                    <tr>
                                        <td>City </td>
                                        <td><code>{{ $property->city }}</code></td>
                                    </tr>
                                    <tr>
                                        <td>State </td>
                                        <td>
                                            <code>
                                                {{ $property['pstate'] ? $property['pstate']['state_name'] : 'N/A' }}
                                            </code>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Postal Code </td>
                                        <td><code>{{ $property->postal_code }}</code></td>
                                    </tr>

                                    <tr>
                                        <td>Main Image</td>
                                        <td><img src="{{ asset($property->property_thambnail) }}" alt=""
                                                style="width:100px; height:70px;"></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td>Status </td>
                                        <td>
                                            @if ($property->status == 1)
                                                <span class="badge rounded-pill bg-success">Active</span>
                                            @else
                                                <span class="badge rounded-pill bg-danger">InActive</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Property Code </td>
                                        <td><code>{{ $property->property_code }}</code></td>
                                    </tr>

                                    <tr>
                                        <td>Property Size </td>
                                        <td><code>{{ $property->property_size }}</code></td>
                                    </tr>


                                    <tr>
                                        <td>Property Video</td>
                                        <td><code>{{ $property->property_video }}</code></td>
                                    </tr>

                                    <tr>
                                        <td>Neighborhood </td>
                                        <td><code>{{ $property->neighborhood }}</code></td>
                                    </tr>

                                    <tr>
                                        <td>Latitude </td>
                                        <td><code>{{ $property->latitude }}</code></td>
                                    </tr>


                                    <tr>
                                        <td>Longitude </td>
                                        <td><code>{{ $property->longitude }}</code></td>
                                    </tr>

                                    <tr>
                                        <td>Property Type </td>
                                        <td><code>{{ $property['type']['type_name'] }}</code></td>
                                        {{-- $property['type' is a function of property model] and ['type_name' from PT DB Field name] --}}
                                        {{-- Connection between priorities and property_types table and collect type name from property_types --}}
                                    </tr>

                                    <tr>
                                        <td>Property Amenities</td>
                                        <td>
                                            <div class="amenities-view">
                                                @foreach ($amenities as $ameni)
                                                    @if (in_array($ameni->amenitis_name, $property_ami))
                                                        <span class="badge bg-primary">{{ $ameni->amenitis_name }}</span>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Agent Name</td>
                                        @if ($property->agent_id == null)
                                            <td><code> Admin </code></td>
                                        @else
                                            <td><code> {{ $property['user']['name'] }} </code></td>
                                            {{-- ['user is a function of Property model']['name is user DB column'] --}}
                                        @endif
                                    </tr>

                                    <tr>
                                        <td>Multi Image</td>
                                        @foreach ($multiImage as $img)
                                            <td>
                                                <img src="{{ asset($img->photo_name) }}" alt=""
                                                    style="width:80px; height:50px;">
                                            </td>
                                        @endforeach
                                    </tr>

                                </tbody>

                            </table>


                        </div>
                    </div>




                </div>



            </div>

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td>Short Description </td>
                                        <td><code>{{ $property->short_descp }}</code></td>
                                    </tr>

                                    <tr>
                                        <td>Long Description </td>
                                        <td><code>{!! $property->long_descp !!}</code></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <table>
                            <tbody>
                                <tr>
                                    <td>Action </td>
                                    <td style=" padding-left: 690px; padding-top: 14px;">
                                        <a href="{{ route('agent.edit.property', $property->id) }}"
                                            class="btn btn-inverse-warning" title="Edit"> <i data-feather="edit"></i>
                                        </a>

                                        <a href="{{ route('agent.update.property', $property->id) }}"
                                            class="btn btn-inverse-danger" id="delete" title="Delete"> <i
                                                data-feather="trash-2"></i>
                                        </a>
                                    </td>
                                    {{-- <td style=" padding-left: 3px; padding-top: 16px;">
                                    @if ($property->status == 1)
                                        <form method="post" action=" {{ route('inactive.property') }} ">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $property->id }}">
                                            <button type="submit" class="btn btn-danger">InActive</button>
                                        </form>
                                    @else
                                    <form method="post" action=" {{ route('active.property') }} ">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $property->id }}">
                                        <button type="submit" class="btn btn-success">Active</button>

                                    </form>
                                    @endif
                                </td> --}}
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
