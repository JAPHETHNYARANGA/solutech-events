// services/eventService.js
import { API_BASE_URL, getAuthHeaders } from '~/utils/api'

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

// Admin functions
export const fetchAdminEvents = async (organizationSlug) => {
  return await $fetch(`${API_BASE_URL}/${organizationSlug}/admin/events`, {
    headers: getAuthHeaders()
  })
}

export const createEvent = async (organizationSlug, eventData) => {
    // Log the event data (payload)
    console.log('Event Data:', eventData);
  
    // Get the authorization headers and log them
    const authHeaders = getAuthHeaders();
    console.log('Authorization Headers:', authHeaders);
  
    try {
        return await $fetch(`${API_BASE_URL}/${organizationSlug}/admin/events`, {
          method: 'POST',
          headers: authHeaders,
          body: JSON.stringify(eventData)
        })
      } catch (error) {
        console.error("Error creating event:", error)
        throw new Error("Failed to create event: " + error.message)
      }
      
  }
  

// Add deleteEvent function
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