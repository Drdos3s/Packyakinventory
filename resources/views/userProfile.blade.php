@extends('adminLayout')

@section('pagespecificstyles')
    <!-- vendor page speciic styles-->

@stop

@section('content')
<!-- Main content -->
        <!-- Main content -->
        <section class="content">

          <div class="row">
            <div class="col-md-3">

              <!-- Profile Image -->
              <div class="box box-primary">
                <div class="box-body box-profile">
                  <img class="profile-user-img img-responsive img-circle" src="../../dist/img/user4-128x128.jpg" alt="User profile picture">
                  <h3 class="profile-username text-center">{{$user->name}}</h3>
                  <p class="text-muted text-center">Account Owner</p>

                  <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                      <b>Managers</b> <a class="pull-right">{{count($managers)}}</a>
                    </li>
                    <li class="list-group-item">
                      <b>Purchase Orders</b> <a class="pull-right">543</a>
                    </li>
                    <li class="list-group-item">
                      <b>Something Else</b> <a class="pull-right">13,287</a>
                    </li>
                  </ul>

                  <button type="button" class="btn btn-block btn-primary btn-flat createManagerButton" data-toggle="modal" data-target="#createManagerModal">Create Manager</button>
                </div><!-- /.box-body -->
              </div><!-- /.box -->

              <!-- About Me Box -->
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">About Me</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <strong><i class="fa fa-book margin-r-5"></i>  Education</strong>
                  <p class="text-muted">
                    B.S. in Computer Science from the University of Tennessee at Knoxville
                  </p>

                  <hr>

                  <strong><i class="fa fa-map-marker margin-r-5"></i> Location</strong>
                  <p class="text-muted">Malibu, California</p>

                  <hr>

                  <strong><i class="fa fa-pencil margin-r-5"></i> Skills</strong>
                  <p>
                    <span class="label label-danger">UI Design</span>
                    <span class="label label-success">Coding</span>
                    <span class="label label-info">Javascript</span>
                    <span class="label label-warning">PHP</span>
                    <span class="label label-primary">Node.js</span>
                  </p>

                  <hr>

                  <strong><i class="fa fa-file-text-o margin-r-5"></i> Notes</strong>
                  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fermentum enim neque.</p>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
            <div class="col-md-9">
              <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                  <li class="active"><a href="#timeline" data-toggle="tab">Timeline</a></li>
                  <li><a href="#managers" data-toggle="tab">Managers</a></li>
                  
                  <li><a href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane" id="timeline">
                    <!-- The timeline -->
                    <ul class="timeline timeline-inverse">
                      <!-- timeline time label -->
                      <li class="time-label">
                        <span class="bg-red">
                          10 Feb. 2014
                        </span>
                      </li>
                      <!-- /.timeline-label -->
                      <!-- timeline item -->
                      <li>
                        <i class="fa fa-envelope bg-blue"></i>
                        <div class="timeline-item">
                          <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>
                          <h3 class="timeline-header"><a href="#">Support Team</a> sent you an email</h3>
                          <div class="timeline-body">
                            Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                            weebly ning heekya handango imeem plugg dopplr jibjab, movity
                            jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
                            quora plaxo ideeli hulu weebly balihoo...
                          </div>
                          <div class="timeline-footer">
                            <a class="btn btn-primary btn-xs">Read more</a>
                            <a class="btn btn-danger btn-xs">Delete</a>
                          </div>
                        </div>
                      </li>
                      <!-- END timeline item -->
                      <!-- timeline item -->
                      <li>
                        <i class="fa fa-user bg-aqua"></i>
                        <div class="timeline-item">
                          <span class="time"><i class="fa fa-clock-o"></i> 5 mins ago</span>
                          <h3 class="timeline-header no-border"><a href="#">Sarah Young</a> accepted your friend request</h3>
                        </div>
                      </li>
                      <!-- END timeline item -->
                      <!-- timeline item -->
                      <li>
                        <i class="fa fa-comments bg-yellow"></i>
                        <div class="timeline-item">
                          <span class="time"><i class="fa fa-clock-o"></i> 27 mins ago</span>
                          <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>
                          <div class="timeline-body">
                            Take me to your leader!
                            Switzerland is small and neutral!
                            We are more like Germany, ambitious and misunderstood!
                          </div>
                          <div class="timeline-footer">
                            <a class="btn btn-warning btn-flat btn-xs">View comment</a>
                          </div>
                        </div>
                      </li>
                      <!-- END timeline item -->
                      <!-- timeline time label -->
                      <li class="time-label">
                        <span class="bg-green">
                          3 Jan. 2014
                        </span>
                      </li>
                      <!-- /.timeline-label -->
                      <!-- timeline item -->
                      <li>
                        <i class="fa fa-camera bg-purple"></i>
                        <div class="timeline-item">
                          <span class="time"><i class="fa fa-clock-o"></i> 2 days ago</span>
                          <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>
                          <div class="timeline-body">
                            <img src="http://placehold.it/150x100" alt="..." class="margin">
                            <img src="http://placehold.it/150x100" alt="..." class="margin">
                            <img src="http://placehold.it/150x100" alt="..." class="margin">
                            <img src="http://placehold.it/150x100" alt="..." class="margin">
                          </div>
                        </div>
                      </li>
                      <!-- END timeline item -->
                      <li>
                        <i class="fa fa-clock-o bg-gray"></i>
                      </li>
                    </ul>
                  </div><!-- /.tab-pane -->

                  
                  <div class="tab-pane" id="managers">
                    @if (count($managers) > 0)
                      @foreach($managers as $manager)
                        <!-- Post -->
                        <div class="post manager" data-managerID="{{ $manager -> id }}">
                          <div class="user-block">
                            <img class="img-circle img-bordered-sm" src="" alt="user image">
                            <span class='username'>
                              <a href="#">{{ $manager -> name }}</a>
                            </span>
                            <span class='description'>{{ $manager -> manager_location }}</span>
                          </div><!-- /.user-block -->
                          
                          <ul class="list-inline">
                            <li><a href="#" class="link-black text-sm"><i class="fa fa-pencil margin-r-5"></i>Edit</a></li>
                            <li class="pull-right"><a href="#" data-managerID="{{ $manager -> id }}" class="deleteManagerLink link-black text-sm"><i class="fa fa-trash-o margin-r-5"></i>Delete</a></li>
                          </ul>
                        </div><!-- /.post -->

                      @endforeach
                    @else
                      <div class="callout callout-info noItemCallout">
                        <h4><i class="icon fa fa-warning"></i> You don't have any managers!</h4>
                        <button type="button" class="btn btn-block btn-primary btn-flat createManagerButton" data-toggle="modal" data-target="#createManagerModal">Create new manager</button>
                      </div>
                    @endif
                  </div><!-- /.tab-pane -->

                  <div class="tab-pane" id="settings">
                    <form class="form-horizontal">
                      <div class="form-group">
                        <label for="inputName" class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-10">
                          <input type="email" class="form-control" id="inputName" placeholder="Name">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputEmail" class="col-sm-2 control-label">Email</label>
                        <div class="col-sm-10">
                          <input type="email" class="form-control" id="inputEmail" placeholder="Email">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputName" class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="inputName" placeholder="Name">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputExperience" class="col-sm-2 control-label">Experience</label>
                        <div class="col-sm-10">
                          <textarea class="form-control" id="inputExperience" placeholder="Experience"></textarea>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="inputSkills" class="col-sm-2 control-label">Skills</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="inputSkills" placeholder="Skills">
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                          <div class="checkbox">
                            <label>
                              <input type="checkbox"> I agree to the <a href="#">terms and conditions</a>
                            </label>
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                          <button type="submit" class="btn btn-danger">Submit</button>
                        </div>
                      </div>
                    </form>
                  </div><!-- /.tab-pane -->
                </div><!-- /.tab-content -->
              </div><!-- /.nav-tabs-custom -->
            </div><!-- /.col -->
          </div><!-- /.row -->

        </section><!-- /.content -->



<!--MODAL MENU FOR CREATING NEW MANAGER FOR THAT ACCOUNT-->
<div class="row">     
  <div class='col-md-12'>
    <div class="modal fade" id="createManagerModal" role="dialog" aria-labelledby="newManager">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Create New Manager</h4>
          </div>
          <div class="modal-body">
          
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="form-horizontal" role="form">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">

              <div class="form-group">
                <label class="col-md-4 control-label">Name</label>
                <div class="col-md-6">
                  <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                </div>
              </div>

              <div class="form-group">
                <label class="col-md-4 control-label">E-Mail Address</label>
                  <div class="col-md-6">
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                  </div>
              </div>

              <div class="form-group">
                <label class="col-md-4 control-label">Password</label>
                <div class="col-md-6">
                  <input type="password" class="form-control" name="password">
                </div>
              </div>

              <div class="form-group">
              <label class="col-md-4 control-label">Confirm Password</label>
                <div class="col-md-6">
                  <input type="password" class="form-control" name="password_confirmation">
                </div>
              </div>

              <div class="form-group">
              <label class="col-md-4 control-label">Location</label>
                <div class="col-md-6">
                  <select class="form-control" name="location">
                    @foreach($locations as $location)
                      <option value="{{ $location['squareID']}}">{{ $location['locationCity']}}</option>
                    @endforeach
                  </select>
                </div>
              </div>


              <div id="formerrors"></div>



              <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                  <a id="newManagerSubmit" class="btn btn-primary">
                  Register
                  </a>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div><!--END MODAL-->

<!--Start Delete Manager Modal-->
<div class="modal modal-danger" id="deleteManagerModal" data-controls-modal="#deleteManagerModal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span></button>
              <h4 class="modal-title">Danger Modal</h4>
          </div>
          <div class="modal-body">
              <p>Are you sure you want to delete this manager permanetly? Doing so will permanetely remove access for that person.</p>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-outline deleteManagerButton">
                <span class="innerText">Delete Manager</span>
                <i class="fa fa-cog fa-spin fa-fw hidden deleteItemSpinner"></i>
                </button>
          </div>
        </div>
        <!-- /.modal-content -->
    </div>
  <!-- /.modal-dialog -->
</div>

@endsection

@section('pagespecificscripts')
    <!-- purchaseOrder page speciic styles-->
    <script src="{{ asset ("/js/packyakJS.js") }}" type="text/javascript"></script>

    <script src="{{ asset ("/js/users.js") }}" type="text/javascript"></script>
@stop