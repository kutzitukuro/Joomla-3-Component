<?php
/*-------------------------------------------------------------------------------------------------------------|  www.vdm.io  |------/
 ____                                                  ____                 __               __               __
/\  _`\                                               /\  _`\   __         /\ \__         __/\ \             /\ \__
\ \,\L\_\     __   _ __    ___ ___     ___     ___    \ \ \/\ \/\_\    ____\ \ ,_\  _ __ /\_\ \ \____  __  __\ \ ,_\   ___   _ __
 \/_\__ \   /'__`\/\`'__\/' __` __`\  / __`\ /' _ `\   \ \ \ \ \/\ \  /',__\\ \ \/ /\`'__\/\ \ \ '__`\/\ \/\ \\ \ \/  / __`\/\`'__\
   /\ \L\ \/\  __/\ \ \/ /\ \/\ \/\ \/\ \L\ \/\ \/\ \   \ \ \_\ \ \ \/\__, `\\ \ \_\ \ \/ \ \ \ \ \L\ \ \ \_\ \\ \ \_/\ \L\ \ \ \/
   \ `\____\ \____\\ \_\ \ \_\ \_\ \_\ \____/\ \_\ \_\   \ \____/\ \_\/\____/ \ \__\\ \_\  \ \_\ \_,__/\ \____/ \ \__\ \____/\ \_\
    \/_____/\/____/ \/_/  \/_/\/_/\/_/\/___/  \/_/\/_/    \/___/  \/_/\/___/   \/__/ \/_/   \/_/\/___/  \/___/   \/__/\/___/  \/_/

/------------------------------------------------------------------------------------------------------------------------------------/

	@version		2.0.x
	@created		22nd October, 2015
	@package		Sermon Distributor
	@subpackage		script.php
	@author			Llewellyn van der Merwe <https://www.vdm.io/>	
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html 
	
	A sermon distributor that links to Dropbox. 
                                                             
/----------------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.modal');

/**
 * Script File of Sermondistributor Component
 */
class com_sermondistributorInstallerScript
{
	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 */
	public function __construct(JAdapterInstance $parent) {}

	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(JAdapterInstance $parent) {}

	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 */
	public function uninstall(JAdapterInstance $parent)
	{
		// Get Application object
		$app = JFactory::getApplication();

		// Get The Database object
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Preacher alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.preacher') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$preacher_found = $db->getNumRows();
		// Now check if there were any rows
		if ($preacher_found)
		{
			// Since there are load the needed  preacher type ids
			$preacher_ids = $db->loadColumn();
			// Remove Preacher from the content type table
			$preacher_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.preacher') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($preacher_condition);
			$db->setQuery($query);
			// Execute the query to remove Preacher items
			$preacher_done = $db->execute();
			if ($preacher_done)
			{
				// If succesfully remove Preacher add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.preacher) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Preacher items from the contentitem tag map table
			$preacher_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.preacher') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($preacher_condition);
			$db->setQuery($query);
			// Execute the query to remove Preacher items
			$preacher_done = $db->execute();
			if ($preacher_done)
			{
				// If succesfully remove Preacher add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.preacher) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Preacher items from the ucm content table
			$preacher_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_sermondistributor.preacher') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($preacher_condition);
			$db->setQuery($query);
			// Execute the query to remove Preacher items
			$preacher_done = $db->execute();
			if ($preacher_done)
			{
				// If succesfully remove Preacher add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.preacher) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Preacher items are cleared from DB
			foreach ($preacher_ids as $preacher_id)
			{
				// Remove Preacher items from the ucm base table
				$preacher_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $preacher_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($preacher_condition);
				$db->setQuery($query);
				// Execute the query to remove Preacher items
				$db->execute();

				// Remove Preacher items from the ucm history table
				$preacher_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $preacher_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($preacher_condition);
				$db->setQuery($query);
				// Execute the query to remove Preacher items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Sermon alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.sermon') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$sermon_found = $db->getNumRows();
		// Now check if there were any rows
		if ($sermon_found)
		{
			// Since there are load the needed  sermon type ids
			$sermon_ids = $db->loadColumn();
			// Remove Sermon from the content type table
			$sermon_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.sermon') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($sermon_condition);
			$db->setQuery($query);
			// Execute the query to remove Sermon items
			$sermon_done = $db->execute();
			if ($sermon_done)
			{
				// If succesfully remove Sermon add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.sermon) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Sermon items from the contentitem tag map table
			$sermon_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.sermon') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($sermon_condition);
			$db->setQuery($query);
			// Execute the query to remove Sermon items
			$sermon_done = $db->execute();
			if ($sermon_done)
			{
				// If succesfully remove Sermon add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.sermon) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Sermon items from the ucm content table
			$sermon_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_sermondistributor.sermon') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($sermon_condition);
			$db->setQuery($query);
			// Execute the query to remove Sermon items
			$sermon_done = $db->execute();
			if ($sermon_done)
			{
				// If succesfully remove Sermon add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.sermon) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Sermon items are cleared from DB
			foreach ($sermon_ids as $sermon_id)
			{
				// Remove Sermon items from the ucm base table
				$sermon_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $sermon_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($sermon_condition);
				$db->setQuery($query);
				// Execute the query to remove Sermon items
				$db->execute();

				// Remove Sermon items from the ucm history table
				$sermon_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $sermon_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($sermon_condition);
				$db->setQuery($query);
				// Execute the query to remove Sermon items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Sermon catid alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.sermons.category') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$sermon_catid_found = $db->getNumRows();
		// Now check if there were any rows
		if ($sermon_catid_found)
		{
			// Since there are load the needed  sermon_catid type ids
			$sermon_catid_ids = $db->loadColumn();
			// Remove Sermon catid from the content type table
			$sermon_catid_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.sermons.category') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($sermon_catid_condition);
			$db->setQuery($query);
			// Execute the query to remove Sermon catid items
			$sermon_catid_done = $db->execute();
			if ($sermon_catid_done)
			{
				// If succesfully remove Sermon catid add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.sermons.category) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Sermon catid items from the contentitem tag map table
			$sermon_catid_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.sermons.category') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($sermon_catid_condition);
			$db->setQuery($query);
			// Execute the query to remove Sermon catid items
			$sermon_catid_done = $db->execute();
			if ($sermon_catid_done)
			{
				// If succesfully remove Sermon catid add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.sermons.category) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Sermon catid items from the ucm content table
			$sermon_catid_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_sermondistributor.sermons.category') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($sermon_catid_condition);
			$db->setQuery($query);
			// Execute the query to remove Sermon catid items
			$sermon_catid_done = $db->execute();
			if ($sermon_catid_done)
			{
				// If succesfully remove Sermon catid add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.sermons.category) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Sermon catid items are cleared from DB
			foreach ($sermon_catid_ids as $sermon_catid_id)
			{
				// Remove Sermon catid items from the ucm base table
				$sermon_catid_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $sermon_catid_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($sermon_catid_condition);
				$db->setQuery($query);
				// Execute the query to remove Sermon catid items
				$db->execute();

				// Remove Sermon catid items from the ucm history table
				$sermon_catid_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $sermon_catid_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($sermon_catid_condition);
				$db->setQuery($query);
				// Execute the query to remove Sermon catid items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Series alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.series') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$series_found = $db->getNumRows();
		// Now check if there were any rows
		if ($series_found)
		{
			// Since there are load the needed  series type ids
			$series_ids = $db->loadColumn();
			// Remove Series from the content type table
			$series_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.series') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($series_condition);
			$db->setQuery($query);
			// Execute the query to remove Series items
			$series_done = $db->execute();
			if ($series_done)
			{
				// If succesfully remove Series add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.series) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Series items from the contentitem tag map table
			$series_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.series') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($series_condition);
			$db->setQuery($query);
			// Execute the query to remove Series items
			$series_done = $db->execute();
			if ($series_done)
			{
				// If succesfully remove Series add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.series) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Series items from the ucm content table
			$series_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_sermondistributor.series') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($series_condition);
			$db->setQuery($query);
			// Execute the query to remove Series items
			$series_done = $db->execute();
			if ($series_done)
			{
				// If succesfully remove Series add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.series) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Series items are cleared from DB
			foreach ($series_ids as $series_id)
			{
				// Remove Series items from the ucm base table
				$series_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $series_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($series_condition);
				$db->setQuery($query);
				// Execute the query to remove Series items
				$db->execute();

				// Remove Series items from the ucm history table
				$series_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $series_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($series_condition);
				$db->setQuery($query);
				// Execute the query to remove Series items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Statistic alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.statistic') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$statistic_found = $db->getNumRows();
		// Now check if there were any rows
		if ($statistic_found)
		{
			// Since there are load the needed  statistic type ids
			$statistic_ids = $db->loadColumn();
			// Remove Statistic from the content type table
			$statistic_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.statistic') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($statistic_condition);
			$db->setQuery($query);
			// Execute the query to remove Statistic items
			$statistic_done = $db->execute();
			if ($statistic_done)
			{
				// If succesfully remove Statistic add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.statistic) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Statistic items from the contentitem tag map table
			$statistic_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.statistic') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($statistic_condition);
			$db->setQuery($query);
			// Execute the query to remove Statistic items
			$statistic_done = $db->execute();
			if ($statistic_done)
			{
				// If succesfully remove Statistic add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.statistic) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Statistic items from the ucm content table
			$statistic_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_sermondistributor.statistic') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($statistic_condition);
			$db->setQuery($query);
			// Execute the query to remove Statistic items
			$statistic_done = $db->execute();
			if ($statistic_done)
			{
				// If succesfully remove Statistic add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.statistic) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Statistic items are cleared from DB
			foreach ($statistic_ids as $statistic_id)
			{
				// Remove Statistic items from the ucm base table
				$statistic_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $statistic_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($statistic_condition);
				$db->setQuery($query);
				// Execute the query to remove Statistic items
				$db->execute();

				// Remove Statistic items from the ucm history table
				$statistic_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $statistic_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($statistic_condition);
				$db->setQuery($query);
				// Execute the query to remove Statistic items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where External_source alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.external_source') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$external_source_found = $db->getNumRows();
		// Now check if there were any rows
		if ($external_source_found)
		{
			// Since there are load the needed  external_source type ids
			$external_source_ids = $db->loadColumn();
			// Remove External_source from the content type table
			$external_source_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.external_source') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($external_source_condition);
			$db->setQuery($query);
			// Execute the query to remove External_source items
			$external_source_done = $db->execute();
			if ($external_source_done)
			{
				// If succesfully remove External_source add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.external_source) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove External_source items from the contentitem tag map table
			$external_source_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.external_source') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($external_source_condition);
			$db->setQuery($query);
			// Execute the query to remove External_source items
			$external_source_done = $db->execute();
			if ($external_source_done)
			{
				// If succesfully remove External_source add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.external_source) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove External_source items from the ucm content table
			$external_source_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_sermondistributor.external_source') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($external_source_condition);
			$db->setQuery($query);
			// Execute the query to remove External_source items
			$external_source_done = $db->execute();
			if ($external_source_done)
			{
				// If succesfully remove External_source add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.external_source) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the External_source items are cleared from DB
			foreach ($external_source_ids as $external_source_id)
			{
				// Remove External_source items from the ucm base table
				$external_source_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $external_source_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($external_source_condition);
				$db->setQuery($query);
				// Execute the query to remove External_source items
				$db->execute();

				// Remove External_source items from the ucm history table
				$external_source_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $external_source_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($external_source_condition);
				$db->setQuery($query);
				// Execute the query to remove External_source items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Local_listing alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.local_listing') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$local_listing_found = $db->getNumRows();
		// Now check if there were any rows
		if ($local_listing_found)
		{
			// Since there are load the needed  local_listing type ids
			$local_listing_ids = $db->loadColumn();
			// Remove Local_listing from the content type table
			$local_listing_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.local_listing') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($local_listing_condition);
			$db->setQuery($query);
			// Execute the query to remove Local_listing items
			$local_listing_done = $db->execute();
			if ($local_listing_done)
			{
				// If succesfully remove Local_listing add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.local_listing) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Local_listing items from the contentitem tag map table
			$local_listing_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.local_listing') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($local_listing_condition);
			$db->setQuery($query);
			// Execute the query to remove Local_listing items
			$local_listing_done = $db->execute();
			if ($local_listing_done)
			{
				// If succesfully remove Local_listing add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.local_listing) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Local_listing items from the ucm content table
			$local_listing_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_sermondistributor.local_listing') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($local_listing_condition);
			$db->setQuery($query);
			// Execute the query to remove Local_listing items
			$local_listing_done = $db->execute();
			if ($local_listing_done)
			{
				// If succesfully remove Local_listing add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.local_listing) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Local_listing items are cleared from DB
			foreach ($local_listing_ids as $local_listing_id)
			{
				// Remove Local_listing items from the ucm base table
				$local_listing_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $local_listing_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($local_listing_condition);
				$db->setQuery($query);
				// Execute the query to remove Local_listing items
				$db->execute();

				// Remove Local_listing items from the ucm history table
				$local_listing_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $local_listing_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($local_listing_condition);
				$db->setQuery($query);
				// Execute the query to remove Local_listing items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Help_document alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.help_document') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$help_document_found = $db->getNumRows();
		// Now check if there were any rows
		if ($help_document_found)
		{
			// Since there are load the needed  help_document type ids
			$help_document_ids = $db->loadColumn();
			// Remove Help_document from the content type table
			$help_document_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.help_document') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($help_document_condition);
			$db->setQuery($query);
			// Execute the query to remove Help_document items
			$help_document_done = $db->execute();
			if ($help_document_done)
			{
				// If succesfully remove Help_document add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.help_document) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Help_document items from the contentitem tag map table
			$help_document_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_sermondistributor.help_document') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($help_document_condition);
			$db->setQuery($query);
			// Execute the query to remove Help_document items
			$help_document_done = $db->execute();
			if ($help_document_done)
			{
				// If succesfully remove Help_document add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.help_document) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Help_document items from the ucm content table
			$help_document_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_sermondistributor.help_document') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($help_document_condition);
			$db->setQuery($query);
			// Execute the query to remove Help_document items
			$help_document_done = $db->execute();
			if ($help_document_done)
			{
				// If succesfully remove Help_document add queued success message.
				$app->enqueueMessage(JText::_('The (com_sermondistributor.help_document) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Help_document items are cleared from DB
			foreach ($help_document_ids as $help_document_id)
			{
				// Remove Help_document items from the ucm base table
				$help_document_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $help_document_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($help_document_condition);
				$db->setQuery($query);
				// Execute the query to remove Help_document items
				$db->execute();

				// Remove Help_document items from the ucm history table
				$help_document_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $help_document_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($help_document_condition);
				$db->setQuery($query);
				// Execute the query to remove Help_document items
				$db->execute();
			}
		}

		// If All related items was removed queued success message.
		$app->enqueueMessage(JText::_('All related items was removed from the <b>#__ucm_base</b> table'));
		$app->enqueueMessage(JText::_('All related items was removed from the <b>#__ucm_history</b> table'));

		// Remove sermondistributor assets from the assets table
		$sermondistributor_condition = array( $db->quoteName('name') . ' LIKE ' . $db->quote('com_sermondistributor%') );

		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__assets'));
		$query->where($sermondistributor_condition);
		$db->setQuery($query);
		$help_document_done = $db->execute();
		if ($help_document_done)
		{
			// If succesfully remove sermondistributor add queued success message.
			$app->enqueueMessage(JText::_('All related items was removed from the <b>#__assets</b> table'));
		}

		// little notice as after service, in case of bad experience with component.
		echo '<h2>Did something go wrong? Are you disappointed?</h2>
		<p>Please let me know at <a href="mailto:joomla@vdm.io">joomla@vdm.io</a>.
		<br />We at Vast Development Method are committed to building extensions that performs proficiently! You can help us, really!
		<br />Send me your thoughts on improvements that is needed, trust me, I will be very grateful!
		<br />Visit us at <a href="https://www.vdm.io/" target="_blank">https://www.vdm.io/</a> today!</p>';
	}

	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update(JAdapterInstance $parent){}

	/**
	 * Called before any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($type, JAdapterInstance $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// is redundant or so it seems ...hmmm let me know if it works again
		if ($type === 'uninstall')
		{
			return true;
		}
		// the default for both install and update
		$jversion = new JVersion();
		if (!$jversion->isCompatible('3.8.0'))
		{
			$app->enqueueMessage('Please upgrade to at least Joomla! 3.8.0 before continuing!', 'error');
			return false;
		}
		// do any updates needed
		if ($type === 'update')
		{
		// load the helper class
		JLoader::register('SermondistributorHelper', JPATH_ADMINISTRATOR . '/components/com_sermondistributor/helpers/sermondistributor.php');
		// check the version of Sermon Distributor
		$manifest = SermondistributorHelper::manifest();
		if (isset($manifest->version) && strpos($manifest->version, '.') !== false)
		{
			$version = explode('.', $manifest->version);
			if ($version[0] == 1 && $version[1] < 4)
			{
				// Get a db connection.
				$db = JFactory::getDbo();
				// Create a new query object.
				$query = $db->getQuery(true);
				// update all manual and auto links in sermons
				$query->select($db->quoteName(array('id', 'manual_files')));
				$query->from($db->quoteName('#__sermondistributor_sermon'));
				$query->where($db->quoteName('source') . ' = 2');
				// Reset the query using our newly populated query object.
				$db->setQuery($query);
				$db->execute();
				if ($db->getNumRows())
				{
					$rows = $db->loadAssocList('id', 'manual_files');
					foreach ($rows as $id => $files)
					{
						if (SermondistributorHelper::checkJson($files))
						{
							$files = json_decode($files, true);
							if (SermondistributorHelper::checkArray($files))
							{
								foreach ($files as $nr => &$file)
								{
									$tmp = str_replace('VDM_pLeK_h0uEr/', '', $file);
									$new = strtolower($tmp);
									// now update the file
									$file = str_replace($tmp, $new, $file);
								}
							}
						}
						// only update if it was fixed
						if (SermondistributorHelper::checkArray($files))
						{
							$object = new stdClass();
							// make sure the files are set to json
							$object->manual_files = json_encode($files);
							$object->id = $id;
							JFactory::getDbo()->updateObject('#__sermondistributor_sermon', $object, 'id');
						}
					}
				}
				// do an update by moving config data to the new external source area.
				$this->comParams = JComponentHelper::getParams('com_sermondistributor');
				// the number of links
				$numbers = range(1, 4);
				// the types of links
				$types = array('auto','manual');
				// the update targets
				$this->updateTargetsU = array();
				$this->updateTargetsF = array();
				// get all listed targets bast on type
				foreach ($types as $type)
				{
					// now check if they are set
					foreach ($numbers as $number)
					{
						// set the number to string
						$numStr = SermondistributorHelper::safeString($number);
						// Get the url
						$url = $this->comParams->get($type.'dropbox'.$numStr, null);
						// only load those that are set
						if ($url && SermondistributorHelper::checkString($url))
						{
							if (!isset($this->updateTargetsU[$type]))
							{
								$this->updateTargetsU[$type] = array();
							}
							$this->updateTargetsU[$type][] = $url;
						}
						// Get the folders if set
						$folder = $this->comParams->get($type.'dropboxfolder'.$numStr, null);
						// only load those that are set
						if ($folder && SermondistributorHelper::checkString($folder))
						{
							if (!isset($this->updateTargetsF[$type]))
							{
								$this->updateTargetsF[$type] = array();
							}
							$this->updateTargetsF[$type][] = $folder;
						}
					}
				}
			}
		}
		}
		// do any install needed
		if ($type === 'install')
		{
		}
		return true;
	}

	/**
	 * Called after any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($type, JAdapterInstance $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// set the default component settings
		if ($type === 'install')
		{

			// Get The Database object
			$db = JFactory::getDbo();

			// Create the preacher content type object.
			$preacher = new stdClass();
			$preacher->type_title = 'Sermondistributor Preacher';
			$preacher->type_alias = 'com_sermondistributor.preacher';
			$preacher->table = '{"special": {"dbtable": "#__sermondistributor_preacher","key": "id","type": "Preacher","prefix": "sermondistributorTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$preacher->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "description","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","website":"website","email":"email","icon":"icon","alias":"alias"}}';
			$preacher->router = 'SermondistributorHelperRoute::getPreacherRoute';
			$preacher->content_history_options = '{"formFile": "administrator/components/com_sermondistributor/models/forms/preacher.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$preacher_Inserted = $db->insertObject('#__content_types', $preacher);

			// Create the sermon content type object.
			$sermon = new stdClass();
			$sermon->type_title = 'Sermondistributor Sermon';
			$sermon->type_alias = 'com_sermondistributor.sermon';
			$sermon->table = '{"special": {"dbtable": "#__sermondistributor_sermon","key": "id","type": "Sermon","prefix": "sermondistributorTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$sermon->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "description","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "catid","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","preacher":"preacher","series":"series","short_description":"short_description","link_type":"link_type","source":"source","local_files":"local_files","alias":"alias","description":"description","tags":"tags","icon":"icon","build":"build","not_required":"not_required","manual_files":"manual_files","auto_sermons":"auto_sermons","url":"url","scripture":"scripture"}}';
			$sermon->router = 'SermondistributorHelperRoute::getSermonRoute';
			$sermon->content_history_options = '{"formFile": "administrator/components/com_sermondistributor/models/forms/sermon.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","not_required","auto_sermons"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","preacher","series","catid","link_type","source","build","not_required"],"displayLookup": [{"sourceColumn": "catid","targetTable": "#__categories","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "preacher","targetTable": "#__sermondistributor_preacher","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "series","targetTable": "#__sermondistributor_series","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$sermon_Inserted = $db->insertObject('#__content_types', $sermon);

			// Create the sermon category content type object.
			$sermon_category = new stdClass();
			$sermon_category->type_title = 'Sermondistributor Sermon Catid';
			$sermon_category->type_alias = 'com_sermondistributor.sermons.category';
			$sermon_category->table = '{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}';
			$sermon_category->field_mappings = '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created_time","core_modified_time":"modified_time","core_body":"description", "core_hits":"hits","core_publish_up":"null","core_publish_down":"null","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"null", "core_urls":"null", "core_version":"version", "core_ordering":"null", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"parent_id", "core_xreference":"null", "asset_id":"asset_id"}, "special":{"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","extension":"extension","note":"note"}}';
			$sermon_category->router = 'SermondistributorHelperRoute::getCategoryRoute';
			$sermon_category->content_history_options = '{"formFile":"administrator\/components\/com_categories\/models\/forms\/category.xml", "hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path","extension"], "ignoreChanges":["modified_user_id", "modified_time", "checked_out", "checked_out_time", "version", "hits", "path"],"convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"created_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"parent_id","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}';

			// Set the object into the content types table.
			$sermon_category_Inserted = $db->insertObject('#__content_types', $sermon_category);

			// Create the series content type object.
			$series = new stdClass();
			$series->type_title = 'Sermondistributor Series';
			$series->type_alias = 'com_sermondistributor.series';
			$series->table = '{"special": {"dbtable": "#__sermondistributor_series","key": "id","type": "Series","prefix": "sermondistributorTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$series->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "description","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","scripture":"scripture","icon":"icon","alias":"alias"}}';
			$series->router = 'SermondistributorHelperRoute::getSeriesRoute';
			$series->content_history_options = '{"formFile": "administrator/components/com_sermondistributor/models/forms/series.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$series_Inserted = $db->insertObject('#__content_types', $series);

			// Create the statistic content type object.
			$statistic = new stdClass();
			$statistic->type_title = 'Sermondistributor Statistic';
			$statistic->type_alias = 'com_sermondistributor.statistic';
			$statistic->table = '{"special": {"dbtable": "#__sermondistributor_statistic","key": "id","type": "Statistic","prefix": "sermondistributorTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$statistic->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "filename","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"filename":"filename","sermon":"sermon","preacher":"preacher","series":"series","counter":"counter"}}';
			$statistic->router = 'SermondistributorHelperRoute::getStatisticRoute';
			$statistic->content_history_options = '{"formFile": "administrator/components/com_sermondistributor/models/forms/statistic.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","sermon","preacher","series","counter"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "sermon","targetTable": "#__sermondistributor_sermon","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "preacher","targetTable": "#__sermondistributor_preacher","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "series","targetTable": "#__sermondistributor_series","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$statistic_Inserted = $db->insertObject('#__content_types', $statistic);

			// Create the external_source content type object.
			$external_source = new stdClass();
			$external_source->type_title = 'Sermondistributor External_source';
			$external_source->type_alias = 'com_sermondistributor.external_source';
			$external_source->table = '{"special": {"dbtable": "#__sermondistributor_external_source","key": "id","type": "External_source","prefix": "sermondistributorTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$external_source->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "description","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "null","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"description":"description","externalsources":"externalsources","update_method":"update_method","filetypes":"filetypes","build":"build","not_required":"not_required","update_timer":"update_timer","dropboxoptions":"dropboxoptions","permissiontype":"permissiontype","oauthtoken":"oauthtoken"}}';
			$external_source->router = 'SermondistributorHelperRoute::getExternal_sourceRoute';
			$external_source->content_history_options = '{"formFile": "administrator/components/com_sermondistributor/models/forms/external_source.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","not_required"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","externalsources","update_method","build","not_required","update_timer","dropboxoptions"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$external_source_Inserted = $db->insertObject('#__content_types', $external_source);

			// Create the local_listing content type object.
			$local_listing = new stdClass();
			$local_listing->type_title = 'Sermondistributor Local_listing';
			$local_listing->type_alias = 'com_sermondistributor.local_listing';
			$local_listing->table = '{"special": {"dbtable": "#__sermondistributor_local_listing","key": "id","type": "Local_listing","prefix": "sermondistributorTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$local_listing->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "null","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","build":"build","size":"size","external_source":"external_source","key":"key","url":"url"}}';
			$local_listing->router = 'SermondistributorHelperRoute::getLocal_listingRoute';
			$local_listing->content_history_options = '{"formFile": "administrator/components/com_sermondistributor/models/forms/local_listing.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","build","size","external_source"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "external_source","targetTable": "#__sermondistributor_external_source","targetColumn": "id","displayColumn": "description"}]}';

			// Set the object into the content types table.
			$local_listing_Inserted = $db->insertObject('#__content_types', $local_listing);

			// Create the help_document content type object.
			$help_document = new stdClass();
			$help_document->type_title = 'Sermondistributor Help_document';
			$help_document->type_alias = 'com_sermondistributor.help_document';
			$help_document->table = '{"special": {"dbtable": "#__sermondistributor_help_document","key": "id","type": "Help_document","prefix": "sermondistributorTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$help_document->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "title","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "content","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"title":"title","type":"type","groups":"groups","location":"location","admin_view":"admin_view","site_view":"site_view","not_required":"not_required","content":"content","article":"article","url":"url","target":"target","alias":"alias"}}';
			$help_document->router = 'SermondistributorHelperRoute::getHelp_documentRoute';
			$help_document->content_history_options = '{"formFile": "administrator/components/com_sermondistributor/models/forms/help_document.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","not_required"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","type","location","not_required","article","target"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "article","targetTable": "#__content","targetColumn": "id","displayColumn": "title"}]}';

			// Set the object into the content types table.
			$help_document_Inserted = $db->insertObject('#__content_types', $help_document);


			// Install the global extenstion assets permission.
			$query = $db->getQuery(true);
			// Field to update.
			$fields = array(
				$db->quoteName('rules') . ' = ' . $db->quote('{"site.preachers.access":{"1":1},"site.preacher.access":{"1":1},"site.categories.access":{"1":1},"site.category.access":{"1":1},"site.serieslist.access":{"1":1},"site.series.access":{"1":1},"site.sermon.access":{"1":1}}'),
			);
			// Condition.
			$conditions = array(
				$db->quoteName('name') . ' = ' . $db->quote('com_sermondistributor')
			);
			$query->update($db->quoteName('#__assets'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$allDone = $db->execute();

			// Install the global extenstion params.
			$query = $db->getQuery(true);
			// Field to update.
			$fields = array(
				$db->quoteName('params') . ' = ' . $db->quote('{"autorName":"Llewellyn van der Merwe","autorEmail":"joomla@vdm.io","player":"1","add_to_button":"0","preachers_display":"2","preachers_list_style":"2","preachers_table_color":"0","preachers_icon":"1","preachers_desc":"1","preachers_sermon_count":"1","preachers_hits":"1","preachers_website":"1","preachers_email":"1","preacher_request_id":"0","preacher_display":"3","preacher_box_contrast":"1","preacher_list_style":"3","preacher_icon":"1","preacher_desc":"1","preacher_sermon_count":"1","preacher_hits":"1","preacher_email":"1","preacher_website":"1","preacher_sermons_display":"2","preacher_sermons_list_style":"2","preacher_sermons_table_color":"0","preacher_sermons_icon":"1","preacher_sermons_desc":"1","preacher_sermons_series":"1","preacher_sermons_category":"1","preacher_sermons_download_counter":"1","preacher_sermons_hits":"1","preacher_sermons_downloads":"1","preacher_sermons_open":"1","categories_display":"2","categories_list_style":"2","categories_table_color":"0","categories_icon":"1","categories_desc":"1","categories_sermon_count":"1","categories_hits":"1","category_display":"3","category_box_contrast":"1","category_list_style":"3","category_icon":"1","category_desc":"1","category_sermon_count":"1","category_hits":"1","category_sermons_display":"2","category_sermons_list_style":"1","category_sermons_table_color":"1","category_sermons_icon":"1","category_sermons_desc":"1","category_sermons_preacher":"1","category_sermons_series":"1","category_sermons_download_counter":"1","category_sermons_hits":"1","category_sermons_downloads":"1","category_sermons_open":"1","list_series_display":"2","list_series_list_style":"2","list_series_table_color":"0","list_series_icon":"1","list_series_desc":"1","list_series_sermon_count":"1","list_series_hits":"1","series_request_id":"0","series_display":"3","series_box_contrast":"1","series_list_style":"3","series_icon":"1","series_desc":"1","series_sermon_count":"1","series_hits":"1","series_sermons_display":"2","series_sermons_list_style":"1","series_sermons_table_color":"1","series_sermons_icon":"1","series_sermons_desc":"1","series_sermons_preacher":"1","series_sermons_category":"1","series_sermons_download_counter":"1","series_sermons_hits":"1","series_sermons_downloads":"1","series_sermons_open":"1","sermon_display":"1","sermon_box_contrast":"1","sermon_list_style":"1","sermon_icon":"1","sermon_desc":"1","sermon_preacher":"1","sermon_series":"1","sermon_category":"1","sermon_download_counter":"1","sermon_hits":"1","sermon_downloads":"1","max_execution_time":"500","check_in":"-1 day","save_history":"1","history_limit":"10","uikit_version":"2","uikit_load":"1","uikit_min":"","uikit_style":""}'),
			);
			// Condition.
			$conditions = array(
				$db->quoteName('element') . ' = ' . $db->quote('com_sermondistributor')
			);
			$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$allDone = $db->execute();

			echo '<a target="_blank" href="https://www.vdm.io/" title="Sermon Distributor">
				<img src="components/com_sermondistributor/assets/images/vdm-component.jpg"/>
				</a>';
		}
		// do any updates needed
		if ($type === 'update')
		{

			// Get The Database object
			$db = JFactory::getDbo();

			// Create the preacher content type object.
			$preacher = new stdClass();
			$preacher->type_title = 'Sermondistributor Preacher';
			$preacher->type_alias = 'com_sermondistributor.preacher';
			$preacher->table = '{"special": {"dbtable": "#__sermondistributor_preacher","key": "id","type": "Preacher","prefix": "sermondistributorTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$preacher->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "description","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","website":"website","email":"email","icon":"icon","alias":"alias"}}';
			$preacher->router = 'SermondistributorHelperRoute::getPreacherRoute';
			$preacher->content_history_options = '{"formFile": "administrator/components/com_sermondistributor/models/forms/preacher.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if preacher type is already in content_type DB.
			$preacher_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($preacher->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$preacher->type_id = $db->loadResult();
				$preacher_Updated = $db->updateObject('#__content_types', $preacher, 'type_id');
			}
			else
			{
				$preacher_Inserted = $db->insertObject('#__content_types', $preacher);
			}

			// Create the sermon content type object.
			$sermon = new stdClass();
			$sermon->type_title = 'Sermondistributor Sermon';
			$sermon->type_alias = 'com_sermondistributor.sermon';
			$sermon->table = '{"special": {"dbtable": "#__sermondistributor_sermon","key": "id","type": "Sermon","prefix": "sermondistributorTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$sermon->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "description","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "catid","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","preacher":"preacher","series":"series","short_description":"short_description","link_type":"link_type","source":"source","local_files":"local_files","alias":"alias","description":"description","tags":"tags","icon":"icon","build":"build","not_required":"not_required","manual_files":"manual_files","auto_sermons":"auto_sermons","url":"url","scripture":"scripture"}}';
			$sermon->router = 'SermondistributorHelperRoute::getSermonRoute';
			$sermon->content_history_options = '{"formFile": "administrator/components/com_sermondistributor/models/forms/sermon.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","not_required","auto_sermons"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","preacher","series","catid","link_type","source","build","not_required"],"displayLookup": [{"sourceColumn": "catid","targetTable": "#__categories","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "preacher","targetTable": "#__sermondistributor_preacher","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "series","targetTable": "#__sermondistributor_series","targetColumn": "id","displayColumn": "name"}]}';

			// Check if sermon type is already in content_type DB.
			$sermon_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($sermon->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$sermon->type_id = $db->loadResult();
				$sermon_Updated = $db->updateObject('#__content_types', $sermon, 'type_id');
			}
			else
			{
				$sermon_Inserted = $db->insertObject('#__content_types', $sermon);
			}

			// Create the sermon category content type object.
			$sermon_category = new stdClass();
			$sermon_category->type_title = 'Sermondistributor Sermon Catid';
			$sermon_category->type_alias = 'com_sermondistributor.sermons.category';
			$sermon_category->table = '{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}';
			$sermon_category->field_mappings = '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created_time","core_modified_time":"modified_time","core_body":"description", "core_hits":"hits","core_publish_up":"null","core_publish_down":"null","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"null", "core_urls":"null", "core_version":"version", "core_ordering":"null", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"parent_id", "core_xreference":"null", "asset_id":"asset_id"}, "special":{"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","extension":"extension","note":"note"}}';
			$sermon_category->router = 'SermondistributorHelperRoute::getCategoryRoute';
			$sermon_category->content_history_options = '{"formFile":"administrator\/components\/com_categories\/models\/forms\/category.xml", "hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path","extension"], "ignoreChanges":["modified_user_id", "modified_time", "checked_out", "checked_out_time", "version", "hits", "path"],"convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"created_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"parent_id","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}';

			// Check if sermon category type is already in content_type DB.
			$sermon_category_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($sermon_category->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$sermon_category->type_id = $db->loadResult();
				$sermon_category_Updated = $db->updateObject('#__content_types', $sermon_category, 'type_id');
			}
			else
			{
				$sermon_category_Inserted = $db->insertObject('#__content_types', $sermon_category);
			}

			// Create the series content type object.
			$series = new stdClass();
			$series->type_title = 'Sermondistributor Series';
			$series->type_alias = 'com_sermondistributor.series';
			$series->table = '{"special": {"dbtable": "#__sermondistributor_series","key": "id","type": "Series","prefix": "sermondistributorTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$series->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "description","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","description":"description","scripture":"scripture","icon":"icon","alias":"alias"}}';
			$series->router = 'SermondistributorHelperRoute::getSeriesRoute';
			$series->content_history_options = '{"formFile": "administrator/components/com_sermondistributor/models/forms/series.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if series type is already in content_type DB.
			$series_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($series->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$series->type_id = $db->loadResult();
				$series_Updated = $db->updateObject('#__content_types', $series, 'type_id');
			}
			else
			{
				$series_Inserted = $db->insertObject('#__content_types', $series);
			}

			// Create the statistic content type object.
			$statistic = new stdClass();
			$statistic->type_title = 'Sermondistributor Statistic';
			$statistic->type_alias = 'com_sermondistributor.statistic';
			$statistic->table = '{"special": {"dbtable": "#__sermondistributor_statistic","key": "id","type": "Statistic","prefix": "sermondistributorTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$statistic->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "filename","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"filename":"filename","sermon":"sermon","preacher":"preacher","series":"series","counter":"counter"}}';
			$statistic->router = 'SermondistributorHelperRoute::getStatisticRoute';
			$statistic->content_history_options = '{"formFile": "administrator/components/com_sermondistributor/models/forms/statistic.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","sermon","preacher","series","counter"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "sermon","targetTable": "#__sermondistributor_sermon","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "preacher","targetTable": "#__sermondistributor_preacher","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "series","targetTable": "#__sermondistributor_series","targetColumn": "id","displayColumn": "name"}]}';

			// Check if statistic type is already in content_type DB.
			$statistic_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($statistic->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$statistic->type_id = $db->loadResult();
				$statistic_Updated = $db->updateObject('#__content_types', $statistic, 'type_id');
			}
			else
			{
				$statistic_Inserted = $db->insertObject('#__content_types', $statistic);
			}

			// Create the external_source content type object.
			$external_source = new stdClass();
			$external_source->type_title = 'Sermondistributor External_source';
			$external_source->type_alias = 'com_sermondistributor.external_source';
			$external_source->table = '{"special": {"dbtable": "#__sermondistributor_external_source","key": "id","type": "External_source","prefix": "sermondistributorTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$external_source->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "description","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "null","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"description":"description","externalsources":"externalsources","update_method":"update_method","filetypes":"filetypes","build":"build","not_required":"not_required","update_timer":"update_timer","dropboxoptions":"dropboxoptions","permissiontype":"permissiontype","oauthtoken":"oauthtoken"}}';
			$external_source->router = 'SermondistributorHelperRoute::getExternal_sourceRoute';
			$external_source->content_history_options = '{"formFile": "administrator/components/com_sermondistributor/models/forms/external_source.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","not_required"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","externalsources","update_method","build","not_required","update_timer","dropboxoptions"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if external_source type is already in content_type DB.
			$external_source_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($external_source->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$external_source->type_id = $db->loadResult();
				$external_source_Updated = $db->updateObject('#__content_types', $external_source, 'type_id');
			}
			else
			{
				$external_source_Inserted = $db->insertObject('#__content_types', $external_source);
			}

			// Create the local_listing content type object.
			$local_listing = new stdClass();
			$local_listing->type_title = 'Sermondistributor Local_listing';
			$local_listing->type_alias = 'com_sermondistributor.local_listing';
			$local_listing->table = '{"special": {"dbtable": "#__sermondistributor_local_listing","key": "id","type": "Local_listing","prefix": "sermondistributorTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$local_listing->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "null","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","build":"build","size":"size","external_source":"external_source","key":"key","url":"url"}}';
			$local_listing->router = 'SermondistributorHelperRoute::getLocal_listingRoute';
			$local_listing->content_history_options = '{"formFile": "administrator/components/com_sermondistributor/models/forms/local_listing.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","build","size","external_source"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "external_source","targetTable": "#__sermondistributor_external_source","targetColumn": "id","displayColumn": "description"}]}';

			// Check if local_listing type is already in content_type DB.
			$local_listing_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($local_listing->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$local_listing->type_id = $db->loadResult();
				$local_listing_Updated = $db->updateObject('#__content_types', $local_listing, 'type_id');
			}
			else
			{
				$local_listing_Inserted = $db->insertObject('#__content_types', $local_listing);
			}

			// Create the help_document content type object.
			$help_document = new stdClass();
			$help_document->type_title = 'Sermondistributor Help_document';
			$help_document->type_alias = 'com_sermondistributor.help_document';
			$help_document->table = '{"special": {"dbtable": "#__sermondistributor_help_document","key": "id","type": "Help_document","prefix": "sermondistributorTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$help_document->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "title","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "content","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"title":"title","type":"type","groups":"groups","location":"location","admin_view":"admin_view","site_view":"site_view","not_required":"not_required","content":"content","article":"article","url":"url","target":"target","alias":"alias"}}';
			$help_document->router = 'SermondistributorHelperRoute::getHelp_documentRoute';
			$help_document->content_history_options = '{"formFile": "administrator/components/com_sermondistributor/models/forms/help_document.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","not_required"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","type","location","not_required","article","target"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "article","targetTable": "#__content","targetColumn": "id","displayColumn": "title"}]}';

			// Check if help_document type is already in content_type DB.
			$help_document_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($help_document->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$help_document->type_id = $db->loadResult();
				$help_document_Updated = $db->updateObject('#__content_types', $help_document, 'type_id');
			}
			else
			{
				$help_document_Inserted = $db->insertObject('#__content_types', $help_document);
			}



			// check if any links were found
			if ((isset($this->updateTargetsU) && SermondistributorHelper::checkArray($this->updateTargetsU)) || (isset($this->updateTargetsF) && SermondistributorHelper::checkArray($this->updateTargetsF)))
			{
				// Get a db connection.
				$db = JFactory::getDbo();

				// get the file types
				$dropbox_filetypes = $this->comParams->get("dropbox_filetypes", null);

				// some defaults
				$user = JFactory::getUser();
				$todayDate = JFactory::getDate()->toSql();

				// now store the old data to the new area
				if (isset($this->updateTargetsU) &&SermondistributorHelper::checkArray($this->updateTargetsU))
				{
					foreach ($this->updateTargetsU as $type => $urls)
					{
						$description = 'Config '. $type . ' url ';
						$buildOption = 1;
						if ('auto' == $type)
						{
							$buildOption = 2;
						}
						$urls = '"'.implode('", "', $urls).'"';
						$data = new stdClass();
						if (SermondistributorHelper::checkArray($dropbox_filetypes))
						{
							$data->filetypes = json_encode($dropbox_filetypes);
						}
						$data->externalsources = 1;
						$data->build = $buildOption;
						$data->description = $description;
						$data->update_method = 1;
						$data->update_timer = 0;
						$data->permissiontype = 'full';
						$data->created = $todayDate;
						$data->created_by = $user->id;
						$data->sharedurl = '{"tsharedurl":['.$urls.']}';
						// add to database
						if ($db->insertObject('#__sermondistributor_external_source', $data))
						{
							$aId = $db->insertid();
							// make sure the access of asset is set
							SermondistributorHelper::setAsset($aId,'external_source');
						}
					}
				}
				if (isset($this->updateTargetsF) && SermondistributorHelper::checkArray($this->updateTargetsF))
				{
					foreach ($this->updateTargetsF as $type => $folder)
					{
						$description = 'Config '. $type . ' folder ';
						$buildOption = 1;
						if ('auto' == $type)
						{
							$buildOption = 2;
						}
						$folder = '"'.implode('", "', $folder).'"';
						$data = new stdClass();
						if (SermondistributorHelper::checkArray($dropbox_filetypes))
						{
							$data->filetypes = json_encode($dropbox_filetypes);
						}
						$data->externalsources = 1;
						$data->build = $buildOption;
						$data->description = $description;
						$data->update_method = 1;
						$data->update_timer = 0;
						$data->permissiontype = 'full';
						$data->created = $todayDate;
						$data->created_by = $user->id;
						$data->folder = '{"tfolder":['.$folder.']}';
						// add to database
						if ($db->insertObject('#__sermondistributor_external_source', $data))
						{
							$aId = $db->insertid();
							// make sure the access of asset is set
							SermondistributorHelper::setAsset($aId,'external_source');
						}
					}
				}
				// Get Application object
				$app = JFactory::getApplication();
				$app->enqueueMessage('Your Dropbox integration has been moved, and can now be viewed at the new external source view. You will now need an APP token to update your local listing of the Dropbox files. Please review the Wiki tab when creating/editing the external source, or open an issue on github if you experience any more difficulties.', 'Info');
			}
			echo '<a target="_blank" href="https://www.vdm.io/" title="Sermon Distributor">
				<img src="components/com_sermondistributor/assets/images/vdm-component.jpg"/>
				</a>
				<h3>Upgrade to Version 2.0.2 Was Successful! Let us know if anything is not working as expected.</h3>';
		}
		return true;
	}
}
