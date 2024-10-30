<?php
/**
 * Plugin Name: Menstrual Cycle Calculator
 * Plugin URI: https://theophilus.com.ng/menstrual-cycle-calculator/
 * Description: The Menstrual Cycle Calculator plugin is designed to help women keep track of their menstrual cycle and fertility window. To use the plugin, simply add the [menstrual_cycle_calculator] shortcode to any post or page.
 * Version: 1.0.2
 * Author: Theophilus Adegbohungbe
 * Author URI: https://theophilus.com.ng
 * Text Domain: menstrual-cycle-calculator
 * Requires at least: 4.7
 * Tested up to: 6.2
 * License: GPL2+ or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

function menstrual_cycle_calculator_styles() {
  wp_enqueue_style( 'menstrual-cycle-calculator-style', plugins_url( 'menstrual-cycle-calculator-styles.css', __FILE__ ) );
}
add_action('wp_enqueue_scripts', 'menstrual_cycle_calculator_styles');


// Create shortcode
function menstrual_cycle_calculator_shortcode() {
  // Check if form has been submitted
  if (isset($_POST['menstrual_cycle_calculator_submit'])) {
    // Sanitize user inputs
    $last_period_date = sanitize_text_field($_POST['last_period_date']);
    $cycle_length = intval(sanitize_text_field($_POST['cycle_length']));
    $period_length = intval(sanitize_text_field($_POST['period_length']));

    // Validate user inputs
    $errors = array();

    // Check if last period date is valid
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $last_period_date)) {
      $errors[] = esc_html__('Please enter a valid date in the format YYYY-MM-DD for the last period date.', 'menstrual-cycle-calculator');
    } else {
      // Check if last period date is in the future
      if (strtotime($last_period_date) > time()) {
        $errors[] = esc_html__('Please enter a last period date that is not in the future.', 'menstrual-cycle-calculator');
      }
    }

    // Check if cycle length is valid
    if ($cycle_length < 1 || $cycle_length > 60) {
      $errors[] = esc_html__('Please enter a cycle length between 1 and 60 days.', 'menstrual-cycle-calculator');
    }

    // Check if period length is valid
    if ($period_length < 1 || $period_length > 10) {
      $errors[] = esc_html__('Please enter a period length between 1 and 10 days.', 'menstrual-cycle-calculator');
    }

    // Display errors if any
    if (!empty($errors)) {
      $output = '<div class="error">';
      foreach ($errors as $error) {
        $output .= '<p>' . $error . '</p>';
      }
      $output .= '</div>';
    } else {
      // Calculate next period date
      $next_period_date = date('Y-m-d', strtotime($last_period_date . ' + ' . $cycle_length . ' days'));

      // Calculate fertility window
      $fertility_window_start = date('Y-m-d', strtotime($next_period_date . ' - ' . ($cycle_length - 10) . ' days'));
      $fertility_window_end = date('Y-m-d', strtotime($next_period_date . ' - 19 days'));

      // Display results
$output = '<div class="menstrual-cycle-calculator-results">';
$output .= '<p>' . esc_html__('Your next period is expected on', 'menstrual-cycle-calculator') . ' ' . $next_period_date . '.</p>';
$output .= '<p>' . esc_html__('Your fertility window is from', 'menstrual-cycle-calculator') . ' ' . $fertility_window_start . ' ' . esc_html__('to', 'menstrual-cycle-calculator') . ' ' . $fertility_window_end . '.</p>';
$output .= '</div>';
}
return $output;
}

// Display form if it hasn't been submitted or if there are errors
ob_start(); ?>

  <div class="menstrual-cycle-calculator-form">
    <form method="post">
      <label for="last_period_date"><?php esc_html_e('Last period date', 'menstrual-cycle-calculator'); ?></label>
      <input type="date" id="last_period_date" name="last_period_date" required>
  <label for="cycle_length"><?php esc_html_e('Cycle length (in days)', 'menstrual-cycle-calculator'); ?></label>
  <input type="number" id="cycle_length" name="cycle_length" required>

  <label for="period_length"><?php esc_html_e('Period length (in days)', 'menstrual-cycle-calculator'); ?></label>
  <input type="number" id="period_length" name="period_length" required>

  <input type="submit" name="menstrual_cycle_calculator_submit" value="<?php esc_attr_e('Calculate', 'menstrual-cycle-calculator'); ?>">
</form>
</div>
  <?php
  return ob_get_clean();
}
add_shortcode('menstrual_cycle_calculator', 'menstrual_cycle_calculator_shortcode');