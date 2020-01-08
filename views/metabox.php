<?php
/**
 * Search metabox markup
 *
 * Note: Styling classes borrowed from "Page Attributes" metabox
 */

use NUSA_Search\Includes\Exclude as Exclude;

$exclude  = Exclude::singleton();
$excluded = $exclude->is_excluded( $post->ID ); // $post object is present from parent file (class-metabox.php)
$weight   = get_post_meta( $post->ID, 'search_weight', true );
?>
<label for="nusa_search_exclude">
	<input type="checkbox" name="nusa_search[exclude]" id="nusa_search_exclude" value="1" <?php echo ( $excluded ) ? 'checked' : ''; ?> />
	Exclude from Search Results
</label>

<p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="nusa_search_weight">Search Weight</label></p>
<input type="number" name="nusa_search[weight]" id="nusa_search_weight" step="0.01" value="<?php echo esc_attr( $weight ); ?>" />
