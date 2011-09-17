<?php
/*
Plugin Name: PCLJ Print Pipeline Overview Generator
Plugin URI: 
Description: Generates CSV's of pipeline overview data
Version: 0.1
Author: Benjamin J. Balter
Author URI: http://ben.balter.com
License: GPL2
*/

class PCLJ_Pipeline {

	function __construct() { 
		add_action ('admin_menu', array(&$this, 'add_menu') );
		add_action( 'admin_init', array(&$this, 'catch_and_generate' ) );
	}
	
	function add_menu() {
		add_submenu_page( 'edit.php?post_type=document', 'Export Pipeline', 'Export Pipeline', 'export_pipeline', 'pclj_pipeline', array( &$this, 'front_end' ) );
	}
	
	function front_end() { ?>
	
	<div class="wrap">
		<h2>Export Pipeline</h2>
		<br />
		<form method="post">
		<?php wp_nonce_field( 'export_pipeline', 'pclj_export' ); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<strong>Issue</strong>
				</th>
				<td>
					<select name="issue" id="issue">
						<option></option>
						<?php $issues = get_terms( 'document_issue', 'hide_empty=0'); 
						foreach ( $issues as $issue ) { ?>
						<option value="<?php echo $issue->term_id; ?>" <?php selected( $issue->term_id, get_option( 'pclj_current_issue' ) ); ?>><?php echo $issue->name; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<strong>Report Type</strong>
				</th>
				<td>
					<input type="radio" name="type" id="pipeline" value="pipeline" checked>
					<label for="pipeline">Pipeline Summary</label><br />
					<input type="radio" name="type" id="article" value="article">
					<label for="article">Article Summary</label>
				</td>
			</tr>
			<tr>
				<th>&nbsp;</th>
				<td>
					<input name="Submit" type="submit" value="Generate Report" class="button-primary"/>
				</td>
			</tr>
		</table>
		
		</form>
	</div>
	<?php
	}
	
	function catch_and_generate() {
		
		if ( !isset( $_POST['pclj_export'] ) )
			return false;
		
		if ( !current_user_can( 'export_pipeline') )
			return false;
		
		$report = $this->generate_report( $_POST['issue'], $_POST['type'] );
		
		$title = $_POST['type'] . '-summary-' . date('n-j-Y');
	
		header("Content-type: text/csv");
		header("Cache-Control: no-store, no-cache");
		header('Content-Disposition: attachment; filename="' . $title .'.csv"');
		$outstream = fopen("php://output",'w');  
		
		foreach ( $report as $row ) {
			fputcsv( $outstream, $row );
		}
		fclose($outstream);  
		exit();
	}
	
	function pipeline_summary( $issueID ) {
		
		$issue = get_term( $issueID, 'document_issue' );
			
		$docs = get_posts( array( 'post_type' => 'document',
								  'post_status' => array( 'private', 'publish', 'draft' ),
								  'document_issue'  => $issue->slug 
						 ) );

		$output = array();
	
		foreach ( $docs as $doc ) {
			$output[] = array(
				'Author' => get_post_meta( $doc->ID, 'document_author', true ),
				'Title' => $doc->post_title,
				'ABA Editor' =>	get_post_meta( $doc->ID, 'document_aba_editor', true ),
				'Student Editor' => $this->get_exclusive_term( $doc->ID, 'document_editor' ),
			);
		}

		return $output;
		
	}
	
	function article_summary( $issueID ) {
	
		$issue = get_term( $issueID, 'document_issue' );
		$docs = get_posts( array( 'post_type' => 'document',
								  'post_status' => array( 'private', 'publish', 'draft' ),
								  'document_issue'  => $issue->slug 
						) );
		$output = array();
		foreach ( $docs as $doc ) {
			$output[] = array( 
				'Author' => get_post_meta( $doc->ID, 'document_author', true ),
				'Abstract' => get_post_meta( $doc->ID, 'document_abstract', true ),		
			);
		}
		return $output;
	}
	
	function generate_report( $issueID, $type ) {
		
		$titles = array( 	'pipeline' => "Pipeline Summary as of " . date( 'F n, Y' ),
							'article' => "Article Summaries" 
						);
		$issue = get_term( $issueID, 'document_issue' );
		
		$output = array();
		$output[] = array( $titles[ $type ] );
		$output[] = array();
		$output[] = array(  'Issue: ' . $issue->name . ' (' . $issue->description . ')' );	
		$output[] = array();
	
		if ( $type == 'pipeline' )
			$report = $this->pipeline_summary( $issueID );
		else if ( $type == 'article' )
			$report = $this->article_summary( $issueID );
		
		//header row
		$output[] = array_keys( $report[0] );
		
		$output = array_merge( $output, $report );
		
		return $output;
		
	}
		
	function get_exclusive_term( $postID, $taxonomy ) {
	
		$terms = wp_get_post_terms( $postID, $taxonomy );
		
		if ( sizeof( $terms ) == 0)
			return false;
					
		return $terms[0]->name;
	}

}

new PCLJ_Pipeline;