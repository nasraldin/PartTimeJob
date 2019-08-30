<?php
/**
 *	Template Name: Apply template
 */
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 *
 * @package boxtheme
 * @subpackage boxtheme
 * @since 1.0
 * @version 1.0
 */
$applied = false;


$job_id = get_query_var('page');

$job = get_post($job_id);
$is_applied = is_applied($job);
if( isset( $_POST['user_email'])  && ! $is_applied ){
	$applied = box_processing_apply($_POST, $job);
}
$user_email = $first_name = $last_name = $phone_number =  '';
if( is_user_logged_in() ){
	global $current_user;
    $current_user = wp_get_current_user();
	$user_email = $current_user->user_email;
	$first_name = $current_user->first_name;
	$last_name = $current_user->last_name;
	$phone_number = get_user_meta( $user_ID,'phone_number', true);
}

?>

<?php get_header(); ?>
<div class="full-width fw-min-height">
	<div class="container site-container">
		<div class=" site-content" id="content" >
			<div class="col-md-12 detail-project">

				<form name="applicationForm"  class="ng-scope ng-isolate-scope ng-invalid ng-invalid-required ng-dirty form-jobapply" method="POST" enctype="multipart/form-data">
					<?php
					if ( $is_applied ){ ?>
						<p class="apply-done">You applied this job.</p>
					<?php } else if( ! $applied ){ ?>
						<?php echo '<h1>'. $job->post_title.'</h1>';?>

						<div model="application.personalDetails" class="ng-isolate-scope">
							<div class="personal-details">

								<h2 class="frm-apply-heading"><?php _e('Start application','boxtheme');?></h2>
								<div class="form-field">
									<label for="user_email">Email</label>
									<input type="email" id="user_email" name="user_email"  class=" ng-pristine ng-invalid ng-invalid-required ng-valid-email ng-valid-maxlength" value="<?php echo esc_attr( $user_email);?>" required />
								</div>
								<div class="sign-in animate ng-isolate-scope ng-hide" ng-show="evaluateAssessment()" email="model.emailAddress" callback="signIn">
									<div name="sign-in-submit" sk-submit="signIn()" class="ng-isolate-scope ng-pristine ng-valid sign-in-submit">
										<div class="sign-in__form">
											<div class="form-group">
												<div class="form-field--half">
													<div class="sign-in__password">
													<label for="signIn-password"><?php _e('Password','boxtheme');?></label><input type="password" id="signIn-password" name="signIn-password" ></div>
												</div>
												<div class="form-field--half">
														<div class="sign-in__stay-signed-in">
															<input type="checkbox" id="staySignedIn" name="staySignedIn">
															<label for="staySignedIn" class="label__inline"><?php _e('Stay signed in','boxtheme');?></label>
														</div>
												</div>
											</div><!-- ngIf: signInError --><!-- ngIf: showRestorePasswordLink -->
											<p class="sign-in__forgot-password ng-scope" ng-if="showRestorePasswordLink"><a href="/sign-in/forgot-password?returnUrl=/"  title="Forgot password?" target="_blank"><?php _e('Forgot password?','boxtheme');?></a></p><!-- end ngIf: showRestorePasswordLink -->
											<div class="sign-in__submit"><button type="submit" data-automation="signIn-btn"><?php _e('Sign in','boxtheme');?></button></div>
										</div>
									</div>
								</div>
							</div>

							<div class="personal-details" > <h2><?php _e('Personal Details','boxtheme');?></h2><!-- sk-not-pattern: matches sign in and register's validation. concatenation of email, phone and dangerous characters regex -->
								<div class="form-group">
									<div class="form-field--half">
										<label for="firstName"><?php _e('First name','boxtheme');?></label>
										<input type="text" id="first_name" value="<?php echo esc_attr($first_name);?>"  name="first_name" label="First name"  >
									</div>
									<div class="form-field--half">
										<label for="lastName"><?php _e('Last name','boxtheme');?></label>
										<input type="text" id="last_name" name="last_name"  value="<?php echo esc_attr($last_name);?>" label="Last name" >
									</div>
								</div>
								<div class="full-width"><div class="form-field--half">
									<label for="phone_number">Phone <span class="label__hint">(<?php _e('mobile preferred','boxtheme');?></span></label>
									<input type="tel" id="phone_number" name="phone_number" value="<?php echo esc_attr($phone_number);?>"  class=""></div>
								</div>
							</div>
						</div><!-- ngIf: canRegister() && isNewUser() && !isAuthorised() -->

						<div class="recent-role" ">
							<h2 class="frm-apply-heading">Most recent role</h2>
							<div class="recent-role__new-to-workforce hide"><input type="checkbox" name="isNewToWorkForce" id="isNewToWorkForce"   class="ng-pristine ng-valid"><label class="label__inline custom__checkbox" for="isNewToWorkForce" ><?php _e('I\'m new to the workforce','boxtheme');?></label></div>
							<div class="form-field"><label for="jobTitle"><?php _e('Job title','boxtheme');?></label>
								<input type="text" id="job_titile" name="job_titile" class="ng-pristine" placeholder="<?php _e('Ex: WordPress Developer','boxtheme');?>" required>
							</div>
							<div class="form-field hide">
								<label for="companyName"><?php _e('Company name','boxtheme');?></label>
								<input type="text" id="companyName" name="companyName"  class="" >
							</div>

							<div class="form-field date-row">

								<label for="jobTitle"><?php _e('Date start','boxtheme');?></label>
								<input type="date" id="date_start" name="date_start" >
							</div>

							<div class="tenure isMobiWeb-false ng-dirty stillInRole" name="timeInRole"  required="required">
								<div class="tenure__still-in-role hide">
									<input type="checkbox" id="stillInRole" name="stillInRole" class="ng-pristine ng-valid">
									<label for="stillInRole" class="label__inline""><?php _e('Still in role','boxtheme');?></label>
								</div>

							</div>
						</div>
						<div class="documents ng-isolate-scope" ng-class="{'disable': !canApply}" model="application" can-apply="canApply()">
							<h2 class="documents__heading frm-apply-heading"><?php _e('Documents for this application','boxtheme');?></h2>

							<div class="documents__advice "><p><?php _e('Files can be up to 2MB for file types .pdf .doc .docx .rtf .txt.','boxtheme');?> </p></div>

							<div class="cover-letter ng-isolate-scope ng-pristine ng-invalid ng-invalid-required" name="coverLetter" >

								<label for="chooseCoverLetter"><?php _e('Cover letter','boxtheme');?></label>
								<div class="document-uploader" >
									<p class="file-selector" name="chooseCoverLetter">
										<label for="chooseCoverLetter-fileSelector" tabindex="0" class="needsclick ng-binding"><i class="ion ion-ios-plus-outline  icon-plus-circle"></i> <?php _e('Upload a cover letter','boxtheme');?></label>
										<input id="chooseCoverLetter-fileSelector" name="cover_letter_file" type="file" >
									</p>
									<p class="warning-message ng-binding ng-hide" ></p>

									<input  id="document-uploader-chooseCoverLetter" name="chooseCoverLetterOpt" type="radio" class="ng-pristine ng-valid " value="1" >
									<label class="document-uploader__label animate ng-hide" for="document-uploader-chooseCoverLetter">
										<span class="document-uploader__filename ng-binding"></span><i  class="document-uploader__indicator ng-hide" ></i>
										<file-downloader ng-show="uploadUri" name="document-chooseCoverLetter-download" file-name="" class="ng-isolate-scope ng-hide">
											<a href="" tabindex="0" class="file-downloader" ng-click="download()"><i class="icon-download" if-browser=""></i></a></file-downloader>
									</label>
								</div>

							<div class="cover-letter-write cv-row ng-isolate-scope ng-pristine ng-valid" name="chooseCoverLetter" >
								<input type="radio" name="chooseCoverLetterOpt" id="writeCoverLetter"    class=" act-radio" value="2" >
								<label class="cover-letter-write__label" for="writeCoverLetter"><?php _e('Write cover letter','boxtheme');?> <i ng-show="isUploading" class="cover-letter-write__indicator ng-hide" ></i> <span class="ng-hide">– <a id="editCoverLetter" href="" ><?php _e('Edit','boxtheme');?></a></span></label>

								<div  class="cover-letter-write__readonly ng-hide"><span class="offscreen"><?php _e('Preview text of written cover letter','boxtheme');?>:</span><p class="cover-letter-write__content ng-binding"></p></div>

								<div class="cover-letter-write__editing animate writer " label="Cover letter" name="chooseCoverLetter" error="error">
									<textarea ng-model="model" id="cover_letter_text" name="cover_letter_text" ng-maxlength="5000"  ></textarea>
									<button id="writtenTextDone65939" class="writer__action" type="button"  aria-label="Save written Cover letter"><?php _e('Save','boxtheme');?></button><a id="writtenTextCancel65939" class="writer__cancel" " href="" ><?php _e('Cancel','boxtheme');?></a><a id="writtenTextClear65939" class="writer__clear ng-hide" href="" ><?php _e('Clear','boxtheme');?></a>
								</div>
							</div> <!-- end write cover letter !-->
							<div class='do-not-cover-letter cv-row'>

								<input name="chooseCoverLetterOpt" type="radio" id="coverLetterDoNotInclude" class="ng-pristine ng-valid act-radio" value="3" >
								<label class="label__radio" for="coverLetterDoNotInclude" ><?php _e('Don\'t include a cover letter','boxtheme');?></label>
							</div>
						</div>


						<div class="resume ng-isolate-scope ng-pristine ng-invalid ng-invalid-required" name="resume" >
							<label for="chooseResume"><?php _e('Resume','boxtheme');?></label>
							<div class="document-uploader ng-isolate-scope ng-pristine ng-valid" label="Upload a resume" >
								<p class="file-selector "  label="Upload a resume"   name="chooseResume">
									<label for="chooseResume-fileSelector" tabindex="0" class="needsclick ng-binding""><i class="ion ion-ios-plus-outline icon-plus-circle"></i><?php _e('Upload a resume','boxtheme');?></label>
									<input id="chooseResume-fileSelector" type="file" name="resume_file" >
								</p>
								<p class="warning-message " ></p><input id="document-uploader-chooseResume" name="chooseResume" type="radio" class="ng-pristine ng-valid ng-hide" value="true" disabled="disabled">
							<label class="document-uploader__label animate ng-hide" for="document-uploader-chooseResume" ><span class="document-uploader__filename ng-binding"></span><i  class="document-uploader__indicator ng-hide" ></i>
								 <file-downloader ng-show="uploadUri" name="document-chooseResume-download" file-name="" class="ng-isolate-scope ng-hide">
								 	<a href="" tabindex="0" class="file-downloader" ng-click="download()" ><i class="icon-download" if-browser=""></i></a>
								 </file-downloader>
								</label>
							</div>

							<div class="resume-list ng-isolate-scop" name="chooseResume"><!-- ngRepeat: resume in resumes --><!-- ngIf: resumes.length > numberOfDisplayedResults --></div><!-- ngIf: !adviceVisible() -->
							<div class="resume-row" >
								<input name="chooseResume" type="radio" id="resumeDoNotInclude" class="ng-pristine ng-valid" value="true"><label class="label__radio" for="resumeDoNotInclude" data-automation="chooseResume-dont-include-label" value = "3"><?php _e('Don\'t include a resume','boxtheme');?></label>
							</div><!-- end ngIf: !adviceVisible() --><!-- ngIf: adviceVisible() -->
						</div>
					</div>

					<div class="submit ng-isolate-scope" profile="isNewUser()" metadata="metadata">
						<button type="submit" data-automation="apply-btn-submit" ><?php _e('Submit application','boxtheme');?></button>
						<div class="submit__privacy-wrapper">
							<small class="submit__privacy" ><?php _e('All personal information submitted by you as part of an application will be used by us in accordance with our <a href="/privacy" target="_blank" data-automation="apply-lnk-privacy">Privacy Statement</a>.','boxtheme');?></small>
						</div>
					</div>
				<?php } else { ?>
					<p class="apply-done"><?php _e('You have applied successful this job ','boxtheme');?></p>
				<?php } ?>
			</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			var html = $(".sign-in-submit").html();
			$(".sign-in-submit").replaceWith($('<form name="sign-in-submit" class="sign-in-submit">' + html + '</form>'));
			$("inpyt[name=chooseCoverLetter-write]").click(function(event){
				console.log(event);
			})
		});
		$(".act-radio").change(function(event){
			var box 	= $(event.currentTarget);
			if( box.attr("id") == "writeCoverLetter" ){
				$(".cover-letter-write__editing").slideDown();
			} else {
				$(".cover-letter-write__editing").slideUp();
			}
		});
		$(".writer__cancel").click(function(event){
			$(".cover-letter-write__editing").slideUp();
			$("#writeCoverLetter").prop('checked', false);
			return false;
		})
	})(jQuery);
</script>
<?php get_footer();?>