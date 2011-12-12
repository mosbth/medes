<?php
/**
 * Interface for modules/addons to the MedesCore. Even parts of MedesCore is modules. 
 *
 * @package MedesCore
 */
interface IModule {
	/**
 	 * Implementing interface IModule. Initiating when module is installed.
 	 */
	public function InstallModule();

	/**
 	 * Implementing interface IModule. Cleaning up when module is deinstalled.
 	 */
	public function DeinstallModule();

	/**
 	 * Implementing interface IModule. Called when updating to newer versions.
 	 */
	public function UpdateModule();
}

