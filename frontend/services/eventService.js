import { API_BASE_URL, getAuthHeaders } from '~/utils/api'

export const fetchOrganizations = async () => {
  return await $fetch(`${API_BASE_URL}/public/organizations`)
}

export const fetchEvents = async (organizationSlug = '') => {
  const url = organizationSlug 
    ? `${API_BASE_URL}/${organizationSlug}/public/events`
    : `${API_BASE_URL}/public/events`
  
  return await $fetch(url)
}

export const fetchEventDetails = async (organizationSlug, eventId) => {
  return await $fetch(`${API_BASE_URL}/${organizationSlug}/public/events/${eventId}`)
}

export const registerForEvent = async (organizationSlug, eventId, attendeeData) => {
  return await $fetch(`${API_BASE_URL}/${organizationSlug}/public/events/${eventId}/register`, {
    method: 'POST',
    body: JSON.stringify(attendeeData)
  })
}

export const fetchAdminEvents = async (organizationSlug) => {
  return await $fetch(`${API_BASE_URL}/${organizationSlug}/admin/events`, {
    headers: getAuthHeaders()
  })
}

export const createEvent = async (organizationSlug, eventData) => {
  const authHeaders = getAuthHeaders();

  // Log the form data (omit sensitive fields if needed)
  console.log("Sending event creation request", {
    organization: organizationSlug,
    eventData, // You can redact or filter fields here if necessary
  });

  try {
    return await $fetch(`${API_BASE_URL}/${organizationSlug}/admin/events`, {
      method: 'POST',
      headers: authHeaders,
      body: JSON.stringify(eventData)
    });
  } catch (error) {
    console.error("Error creating event:", error);
    throw new Error("Failed to create event: " + error.message);
  }
};


export const deleteEvent = async (organizationSlug, eventId) => {
  return await $fetch(`${API_BASE_URL}/${organizationSlug}/admin/events/${eventId}`, {
    method: 'DELETE',
    headers: getAuthHeaders()
  })
}

export const updateEvent = async (organizationSlug, eventId, eventData) => {
  return await $fetch(`${API_BASE_URL}/${organizationSlug}/admin/events/${eventId}`, {
    method: 'PUT',  
    headers: getAuthHeaders(),
    body: JSON.stringify(eventData)
  })
}

export const fetchEventWithAttendees = async (organizationSlug, eventId) => {
  try {
    const [event, attendees] = await Promise.all([
      fetchEventDetails(organizationSlug, eventId),
      $fetch(`${API_BASE_URL}/${organizationSlug}/events/${eventId}/attendees`, {
        headers: getAuthHeaders()
      })
    ]);
    
    return {
      event,
      attendees
    };
  } catch (error) {
    console.error('Error fetching event with attendees:', error);
    throw error;
  }
}

export const fetchEventsWithAttendeeCounts = async (organizationSlug) => {
  try {
    
    const token = localStorage.getItem('authToken');
    if (!token) {
      throw new Error('No authentication token found');
    }

    const response = await fetchAdminEvents(organizationSlug);
    
    const events = Array.isArray(response?.original) ? response.original : [];
    
    if (!events.length) {
      return [];
    }

    
    const eventsWithAttendees = await Promise.all(
      events.map(async (event) => {
        try {          
          const attendeesResponse = await $fetch(
            `${API_BASE_URL}/${organizationSlug}/events/${event.id}/attendees`,
            {
              headers: getAuthHeaders()
            }
          );

          const attendees = Array.isArray(attendeesResponse?.original) 
            ? attendeesResponse.original 
            : (Array.isArray(attendeesResponse) ? attendeesResponse : []);
          
          return {
            ...event,
            attendees_count: attendees.length
          };
        } catch (error) {
          console.error(`[fetchEventsWithAttendeeCounts] Error fetching attendees for event ${event.id}:`, {
            error: error.message,
            stack: error.stack,
            eventId: event.id,
            eventName: event.name
          });
          return {
            ...event,
            attendees_count: 0
          };
        }
      })
    );

    return eventsWithAttendees;
  } catch (error) {
    console.error('[fetchEventsWithAttendeeCounts] Overall function error:', {
      error: error.message,
      stack: error.stack,
      organizationSlug,
      timestamp: new Date().toISOString()
    });
    throw error;
  }
};