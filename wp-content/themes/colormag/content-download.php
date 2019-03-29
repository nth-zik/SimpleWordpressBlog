<?php
/**
 * The template used for displaying page content in download.php
 *
 * @package ThemeCoolBird
 * @subpackage CoolBird
 * @since CoolBird 1.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php do_action( 'colormag_before_post_content' ); ?>
    <h1><?php echo get_the_title();?></h1>
    <div class="col-md-12">
        <image class="rounded img-responsive" style="margin-bottom:10px;" src="<?php echo get_the_post_thumbnail_url();?>">
    </div>
    <div class="row">
    <div class="col-md-6">
        <table class="table table-hover table-dark">
            <tbody>
            <tr>
                <td>Tên phần mềm (Software Name):</td>
                <td><?php echo get_field('software_name',false,false);?></td>
            </tr>
            <tr>
                <td>Giấy phép (License):</td>
                <td><?php echo get_field('software_copyright',false,false);?></td>
            </tr>
            <tr>
                <td>Phiên bản (Version):</td>
                <td><?php echo get_field('software_version',false,false);?></td>
            </tr>
            <tr>
                <td>Kích thước (Size):</td>
                <td><?php echo get_field('software_size',false,false);?></td>
            </tr>
            <tr>
                <td>Hệ điều hành (Requirement):</td>
                <td><?php echo get_field('software_require',false,false);?></td>
            </tr>
            <tr>
                <td>Nhà phát hành (Author):</td>
                <td><?php echo get_field('software_author',false,false);?></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-6">
        <table class="table table-hover table-dark">
            <tr>
                <td><i class="fa fa-download fa-2x" style="color:white; margin-right:10px;"></i>LINK DOWNLOAD</td>
                
            </tr>
            <?php $listLink = explode("\n", get_field('software_linkdownload',false,false));
            for ($i = 0; $i < count($listLink); $i++) {
                echo "<tr><td><a href='".$listLink[$i]."'>".$listLink[$i]."</a></td></tr>";
            }?>
            
        </table>
    </div>
    </div>
	<div class="entry-content clearfix">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before'            => '<div style="clear: both;"></div><div class="pagination clearfix">'.__( 'Pages:', 'colormag' ),
				'after'             => '</div>',
				'link_before'       => '<span>',
				'link_after'        => '</span>'
	      ) );
		?>
	</div>

	<?php do_action( 'colormag_after_post_content' ); ?>
</article>