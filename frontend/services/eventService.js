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
  return await $fetch(`${API_BASE_URL}/${organizationSlug}/admin/events`, {
    method: 'POST',
    headers: getAuthHeaders(),
    body: JSON.stringify(eventData)
  })
}