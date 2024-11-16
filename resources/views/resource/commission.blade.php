@extends('layouts.app')
@section('title', 'Scheme Manager')
@section('pagetitle', 'Scheme Manager')

@section('content')
<div class="content">

    <div class="row">

        <div class="col-sm-12 col-lg-12">
            <div class="iq-card">
                <div class="iq-card-header d-flex justify-content-between">
                    <div class="iq-header-title">
                        <h4 class="card-title">My Commission</h4>
                    </div>
                </div>
                <div class="iq-card-body">
                    <ul class="nav nav-pills nav-tabs mb-3" id="pills-tab" role="tablist">
                        @foreach ($commission as $key => $value)
                        <li class="nav-item">
                            <a class="nav-link {{($key == 'mobile') ? 'active' : ''}}" id="pills-home-tab" data-toggle="pill" href="#{{$key}}" role="tab" aria-controls="pills-home" aria-selected="true">{{ucfirst($key)}}</a>
                        </li>
                        @endforeach
                    </ul>
                    <div class="tab-content" id="pills-tabContent-2">

                        @if(isset($mydata['schememanager']) && $mydata['schememanager']->value == "admin")
                        @foreach ($commission as $key => $value)
                        <div class="tab-pane fade show {{($key == 'mobile') ? 'active' : ''}}" id="{{$key}}" role="tabpanel" aria-labelledby="pills-home-tab">

                            <div class="table-responsive">
                                <table class="table">
                                      <thead class="thead-light">
                                        <th>Provider</th>
                                        <th>Type</th>
                                        @if(Myhelper::hasRole('whitelable'))
                                        <th>Whitelable</th>
                                        @endif
                                        @if(Myhelper::hasRole('md'))
                                        <th>Md</th>
                                        @endif
                                        @if(Myhelper::hasRole('distributor'))
                                        <th>Distributor</th>
                                        @endif
                                        @if(Myhelper::hasRole('retailer'))
                                        <th>Retailer</th>
                                        @endif
                                    </thead>

                                    <tbody>
                                        @foreach ($value as $comm)
                                        <tr>
                                            <td>{{ucfirst($comm->provider->name)}}</td>
                                            <td>{{ucfirst($comm->type)}}</td>
                                            @if(Myhelper::hasRole('whitelable'))
                                            <td>{{ucfirst($comm->whitelable)}}</td>
                                            @endif
                                            @if(Myhelper::hasRole('md'))
                                            <td>{{ucfirst($comm->md)}}</td>
                                            @endif
                                            @if(Myhelper::hasRole('distributor'))
                                            <td>{{ucfirst($comm->distributor)}}</td>
                                            @endif
                                            @if(Myhelper::hasRole('retailer'))
                                            <td>{{ucfirst($comm->retailer)}}</td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endforeach
                        @else
                        @foreach ($commission as $key => $value)
                        <div class="tab-pane fade show {{($key == 'mobile') ? 'active' : ''}}" id="{{$key}}" role="tabpanel" aria-labelledby="pills-home-tab">
                            <div class="table-responsive">
                                <table class="table">
                                      <thead class="thead-light">
                                        <th>Provider</th>
                                        <th>Type</th>
                                        <th>Value</th>
                                    </thead>

                                    <tbody>
                                        @foreach ($value as $comm)
                                        <tr>
                                            <td>{{ucfirst($comm->provider->name)}}</td>
                                            <td>{{ucfirst($comm->type)}}</td>
                                            <td>{{ucfirst($comm->value)}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endforeach
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection