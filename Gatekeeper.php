<?php

namespace Behance\NBD\Gatekeeper;

use Behance\NBD\Gatekeeper\Rules\PercentageRule;

class Gatekeeper {

  /**
   * @var \Behance\NBD\Gatekeeper\RulesetProviderInterface
   */
  private $_ruleset_provider;

  /**
   * @param \Behance\NBD\Gatekeeper\RulesetProviderInterface $ruleset_provider
   */
  public function __construct( RulesetProviderInterface $ruleset_provider ) {

    $this->_ruleset_provider = $ruleset_provider;

  } // __construct

  /**
   * @param  string $feature
   * @param  string|array|null $identifier
   *
   * @return bool
   */
  public function canAccess( $feature, $identifier = null ) {

    $identifier = ( is_array( $identifier ) )
                  ? $identifier
                  : [ $identifier ];

    foreach ( $identifier as $single_identifier ) {
      if ( $this->_ruleset_provider->getRuleset( $feature )->canAccess( $single_identifier ) ) {
        return true;
      }
    }

    return false;

  } // canAccess

  /**
   * @param  string|array|null $identifier
   *
   * @return array
   */
  public function getActiveFeatures( $identifier = null ) {

    return array_filter( $this->_ruleset_provider->getFeatures(), function( $feature_name ) use ( $identifier ) {
      return $this->canAccess( $feature_name, $identifier );
    } );

  } // getActiveFeatures

  /**
   * @param string|array|null $identifier
   *
   * @return array
   */
  public function getPercentageFeaturesByActiveState( $identifier = null ) {

    return $this->_getRuleTypeFeaturesByActiveState( PercentageRule::RULE_NAME, $identifier );

  } // getPercentageFeaturesByActiveState

  /**
   * @param string            $rule_type
   * @param string|array|null $identifier
   *
   * @return array
   */
  private function _getRuleTypeFeaturesByActiveState( $rule_type, $identifier = null ) {

    $feature_state_map = [];
    $features          = $this->_ruleset_provider->getFeatures();

    foreach ( $features as $feature ) {

      $ruleset = $this->_ruleset_provider->getRuleset( $feature );

      if ( $ruleset->hasRuleOfType( $rule_type ) ) {
        $feature_state_map[ $feature ] = $this->canAccess( $feature, $identifier );
      }

    } // foreach features

    return $feature_state_map;

  } // _getRuleTypeFeaturesByActiveState

} // Gatekeeper
