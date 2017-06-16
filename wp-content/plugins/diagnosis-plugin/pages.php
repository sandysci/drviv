<?php

//require plugin_dir_path( dirname( __FILE__ ) ) . 'diagnosis-plugin/database.php';
class DiagnosisPage{

	public function __construct(){

	} 

	function diagnosis_page_function(){
	    
	    if(!current_user_can('manage_options')){
	        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	    }
	    /*add any form processing code here in PHP:*/
	    echo '
	    <div class="container" style ="margin-top:20px">
	    <div class="well well-lg">
	    <h3><span style="position:relative;top:-7px">Insert this  shortcut into any page "[diagnosis_form]"</span></h3></div></div>';
	  }
	  
	function diagnosis_page2_function(){
	    global $wpdb;
	    $diagnosis= $wpdb->get_results( "SELECT * FROM wp_diagnosis ORDER BY created_at DESC" );
		//var_dump($diagnosis);
	    if(!current_user_can('manage_options')){
	       wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	    }
	    
	    echo '
	   <div class="container" style ="margin-top:20px">
	   <div class="well well-lg">
		  <h2>Diagnosis</h2>
		  <p>Diagnosis List</p>            
		  <table class="table">
		    <thead>
		      <tr>
		        <th>ID</th>
		        <th>Firstname</th>
		        <th>Lastname</th>
		        <th>Email</th>
		        <th>Mobile</th>
		        <th>Status</th>
		        <th>Date Booked</th>
		      </tr>
		    </thead>';
	    if(isset($diagnosis) && count($diagnosis) > 0 ){
	    	echo "<tbody>";
	    	foreach ($diagnosis as $key => $value) {
	    		# code...
				      //<a href ='admin.php?page=diagnosis_detail_page'>
				      echo
				      	"<tr style='color:ash; font-family:courier'>
				        <td><a style='color:inherit' href = 'admin.php?page=diagnosis_detail_page&id=$value->ID'>$value->ID</a></td>
				        <td><a href = 'admin.php?page=diagnosis_detail_page&id=$value->ID'>$value->first_name</a></td>
				        <td><a href = 'admin.php?page=diagnosis_detail_page&id==$value->ID'>$value->last_name</a></td>
				        <td><a href = 'admin.php?page=diagnosis_detail_page&id=$value->ID'>$value->email</a></td>
				        <td><a href = 'admin.php?page=diagnosis_detail_page&id=$value->ID'>$value->mobile</a></td>
				        <td><a href = 'admin.php?page=diagnosis_detail_page&id=$value->ID'>$value->status</a></td>
				        <td><a href = 'admin.php?page=diagnosis_detail_page&id=$value->ID'>$value->booked_date</a></td>
				      </tr>";
				      //</a>
			    }
			    echo " </tbody>
			    </table>
				</div>
				</div>";
	    }
	    else{
	    	echo'
		    <tbody>
		      <tr>
		        <td>No Record found</td>
		        <td>No Record found</td>
		        <td>No Record found</td>
		        <td>No Record found</td>
		        <td>No Record found</td>
		        <td>No Record found</td>
		        <td>No Record found</td>
		      </tr>
		    </tbody>
	    </table>
		</div>
		</div>';
	   }
	}
	function diagnosis_detail_function(){
	    global $wpdb;
	    if(!current_user_can('manage_options')){
	        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	    }
	    /*add any form processing code here in PHP:*/
	    if(isset($_GET['id']) && is_numeric($_GET['id'])){
			 $id = $_GET['id'];
			 $diagnosisdetail = $wpdb->get_results("SELECT d.first_name,d.last_name,d.email,d.booked_date,d.mobile,d.status,d.comments, GROUP_CONCAT(du.file_url,'---') as fileurl FROM wp_diagnosis_uploads AS du 
				INNER JOIN wp_diagnosis AS d ON du.diagnosis_id = d.ID WHERE d.ID = $id AND du.diagnosis_id = $id GROUP BY du.diagnosis_id");

			 if(count($diagnosisdetail)  > 0){
			 	//var_dump(plugin_dir_url( __FILE__ ));
	        	$files = $diagnosisdetail[0]->fileurl;
	        	$file_url = explode('---', $files);
	        	//var_dump($file_url);
	        	echo '
			 	<div class="container">
					<div class="row">
					<h2>Diagnosis Detail </h2>
						<div class="col-md-7 ">

					<div class="panel panel-default">
					  <div class="panel-heading">  <h4 >User Profile</h4></div>
					   <div class="panel-body">
					       
					    <div class="box box-info">
					        
				            <div class="box-body">';
				            if(isset($file_url) && count($file_url) >0)
				            {	
					          for ($i = 0 ; $i < count($file_url) - 1 ; $i++) {
					          	if (strpos($file_url[$i], ',') !== false) {
								   $file_url[$i] = str_replace(",","",$file_url[$i]);
								}
						        echo '<div class="col-sm-6">
			                    <div  align="center"> <img alt="User Pic" src='.wp_upload_dir()['url'].'/'.$file_url[$i].' id="profile-image1" class="img-circle img-responsive"> 
						         </div>
						              
					              <br>
					            </div>';
					           }
				            }
				            echo '
		                    
					            <div class="col-sm-6">
					              
					            </div>
					            <div class="clearfix"></div>
					            <hr style="margin:5px 0 5px 0;">
						    
						              
								<div class="col-sm-5 col-xs-6 tital " >First Name:</div><div class="col-sm-7 col-xs-6 ">'.
								$diagnosisdetail[0]->first_name .'</div>
								     <div class="clearfix"></div>
								<div class="bot-border"></div>

								<div class="col-sm-5 col-xs-6 tital " >Last Name:</div><div class="col-sm-7">'. $diagnosisdetail[0]->last_name .' </div>
								  <div class="clearfix"></div>
								<div class="bot-border"></div>

								<div class="col-sm-5 col-xs-6 tital " >Date Of Booking:</div><div class="col-sm-7">'.$diagnosisdetail[0]->booked_date . '</div>

								 <div class="clearfix"></div>
								<div class="bot-border"></div>

								<div class="col-sm-5 col-xs-6 tital " >Email:</div><div class="col-sm-7">' .$diagnosisdetail[0]->email . '</div>

								  <div class="clearfix"></div>
								<div class="bot-border"></div>

								<div class="col-sm-5 col-xs-6 tital " >Mobile:</div><div class="col-sm-7">'. $diagnosisdetail[0]->mobile .'</div>

								 <div class="clearfix"></div>
								<div class="bot-border"></div>

								<div class="col-sm-5 col-xs-6 tital " >Status:</div><div class="col-sm-7">'.$diagnosisdetail[0]->status.'</div>

								 <div class="clearfix"></div>
								<div class="bot-border"></div>

								<div class="col-sm-5 col-xs-6 tital " >Comments:</div><div class="col-sm-7">'.$diagnosisdetail[0]->comments.'</div>
								<div><a href =admin.php?page=diagnosis_chat_page&daignosis_id='.$id.'> Send private message</a> </div>

						            <!-- /.box-body -->
						          </div>
						          <!-- /.box -->
						        </div>    
						    </div> 
						    </div>
						</div> 	';
				
				}

			 else if( count($diagnosisdetail)  == 0){
		     	$diagnosis = $wpdb->get_row("SELECT * FROM wp_diagnosis where ID = $id");
		     	if(count($diagnosis)>0){
		     		echo '
			 	<div class="container">
					<div class="row">
					<h2>Diagnosis Detail </h2>
						<div class="col-md-7 ">

					<div class="panel panel-default">
					  <div class="panel-heading">  <h4 >User Profile</h4></div>
					   <div class="panel-body">
					       
					    <div class="box box-info">
					        
				            <div class="box-body">
		                    
					            <div class="col-sm-6">
					              
					            </div>
					            <div class="clearfix"></div>
					            <hr style="margin:5px 0 5px 0;">
						    
						              
								<div class="col-sm-5 col-xs-6 tital " >First Name:</div><div class="col-sm-7 col-xs-6 ">'.
								$diagnosis[0]->first_name .'</div>
								     <div class="clearfix"></div>
								<div class="bot-border"></div>

								<div class="col-sm-5 col-xs-6 tital " >Last Name:</div><div class="col-sm-7">'. $diagnosis[0]->last_name .' </div>
								  <div class="clearfix"></div>
								<div class="bot-border"></div>

								<div class="col-sm-5 col-xs-6 tital " >Date Of Booking:</div><div class="col-sm-7">'.$diagnosis[0]->booked_date . '</div>diagnosis
								 <div class="clearfix"></div>
								<div class="bot-border"></div>

								<div class="col-sm-5 col-xs-6 tital " >Email:</div><div class="col-sm-7">' .$diagnosis[0]->email . '</div>

								  <div class="clearfix"></div>
								<div class="bot-border"></div>

								<div class="col-sm-5 col-xs-6 tital " >Mobile:</div><div class="col-sm-7">'. $diagnosis[0]->mobile .'</div>

								 <div class="clearfix"></div>
								<div class="bot-border"></div>

								<div class="col-sm-5 col-xs-6 tital " >Status:</div><div class="col-sm-7">'.$diagnosis[0]->status.'</div>

								 <div class="clearfix"></div>
								<div class="bot-border"></div>

								<div class="col-sm-5 col-xs-6 tital " >Comments:</div><div class="col-sm-7">'.$diagnosis[0]->comments.'</div>


						            <!-- /.box-body -->
						          </div>
						          <!-- /.box -->
						        </div>    
						    </div> 
						    </div>
						</div> 	';
		     	}
		     	else{
		     		wp_die( "No Result found");
		     		
		     	}

		     }
		  else{
	     	wp_die( "No Result found");
	      }
	     }

	     else{
	     	wp_die( "No Result found");
	     }
	   
	  }

	  function diagnosis_chat_function(){

	  	$database = new DignosisDB();
	    
	  	if(!current_user_can('manage_options')){
	        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	    }
	    if(isset($_GET['daignosis_id']) && !empty($_GET['daignosis_id']) && is_numeric($_GET['daignosis_id'])){
	    	$id = $_GET['daignosis_id'];
	    	$messages = $database->getDiagnosisMessage($id);
	    	echo '<div class="container">
					<div class="row " style="padding-top:40px;">
				
					    <br />
					   <div class="col-md-8">
				        <div class="panel panel-info">
				            <div class="panel-heading">
				                DIAGNOSIS CHAT HISTORY
				            </div>
				        <div class="panel-body">';
				    if(count($messages) > 0){
					    echo '<ul class="media-list">';
						    foreach ($messages as $key => $message) {
						      	echo $message;
						      	# code...
							  echo' <li class="media">
					            <div class="media-body">
				                   <div class="media">
					                    <a class="pull-left" href="#">
					                        <img class="media-object img-circle " src="assets/img/user.png" />
					                    </a>
				                    <div class="media-body" >
				                        Donec sit amet ligula enim. Duis vel condimentum massa.
				      						Donec sit amet ligula enim. Duis vel condimentum massa.Donec sit amet ligula enim. 
				                                Duis vel condimentum massa.
				                                Donec sit amet ligula enim. Duis vel condimentum massa.
				                                <br />
				                                <small class="text-muted">Alex Deo | 23rd June at 5:00pm</small>
				                                <hr />
				                          </div>
				                    </div>
				                   </div>
			    				</li>';
			    			}
	                 	echo '</ul>';
				    }
				    else{
				    	echo '<ul class="media-list">
					   <li class="media">
			            <div class="media-body">
		                   <div class="media">
		                    <div class="media-body" >
		                        No Message yet......
		                          </div>
		                    </div>
		                   </div>
	    				</li>
                 </ul>';
				    }
				echo'
            </div>
            <div class="panel-footer">
                <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                <div class="input-group">
                    <input type="text" class="form-control" required placeholder="Enter Message" name ="message" />
                    <span class="input-group-btn">
                        <input type="submit" class="btn btn-info" name ="submit">SEND</button>
                    </span>
            	</div>
                </form>
            </div>
        </div>
    </div>';
    if(isset($_POST['submit'])){
    	// echo "hellosandy";
    	$message =  sanitize_text_field($_POST['message']);
    	$diagnosis_id = $id;
    	$database->storeDiagnosisMessage($id,$message);

    }
    echo '
    <div class="col-md-4">
          <div class="panel panel-primary">
            <div class="panel-heading">
               ONLINE USERS
            </div>
            <div class="panel-body">
                <ul class="media-list">
                	<li class="media">
	                    <div class="media-body">
	                        <div class="media">
	                            <a class="pull-left" href="#">
	                                <img class="media-object img-circle" style="max-height:40px;" src="assets/img/user.png" />
	                            </a>
	                            <div class="media-body" >
	                                <h5>Alex Deo | User </h5>
	                         	    <small class="text-muted">Active From 3 hours</small>
	                            </div>
	                        </div>
	                    </div>
	                </li>
                 </ul>
                </div>
             </div>      
    </div>
</div>
  </div>
';
	    	
	    }
	    else{
	    	wp_die( __( 'You cant access this page.' ) );
	    }

	  }
}
?>